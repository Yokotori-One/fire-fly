<?php
declare(strict_types = 1);
/**
 * RuleController.php
 * Copyright (C) 2016 Sander Dorigo
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace FireflyIII\Http\Controllers;

use Auth;
use FireflyIII\Http\Requests\RuleFormRequest;
use FireflyIII\Models\Rule;
use FireflyIII\Models\RuleAction;
use FireflyIII\Models\RuleGroup;
use FireflyIII\Models\RuleTrigger;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Models\TransactionType;
use FireflyIII\Repositories\Rule\RuleRepositoryInterface;
use FireflyIII\Repositories\RuleGroup\RuleGroupRepositoryInterface;
use FireflyIII\Rules\Processor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Input;
use Preferences;
use Response;
use Session;
use URL;
use View;
use Log;

/**
 * Class RuleController
 *
 * @package FireflyIII\Http\Controllers
 */
class RuleController extends Controller
{
    /**
     * RuleController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        View::share('title', trans('firefly.rules'));
        View::share('mainTitleIcon', 'fa-random');
    }

    /**
     * @param RuleGroup $ruleGroup
     *
     * @return View
     */
    public function create(RuleGroup $ruleGroup)
    {
        // count for possible present previous entered triggers/actions.
        $triggerCount = 0;
        $actionCount  = 0;

        // collection of those triggers/actions.
        $oldTriggers = [];
        $oldActions  = [];

        // has old input?
        if (Input::old()) {
            // process old triggers.
            $oldTriggers  = $this->getPreviousTriggers();
            $triggerCount = count($oldTriggers);

            // process old actions
            $oldActions  = $this->getPreviousActions();
            $actionCount = count($oldActions);
        }

        $subTitleIcon = 'fa-clone';
        $subTitle     = trans('firefly.make_new_rule', ['title' => $ruleGroup->title]);

        // put previous url in session if not redirect from store (not "create another").
        if (session('rules.rule.create.fromStore') !== true) {
            Session::put('rules.rule.create.url', URL::previous());
        }
        Session::forget('rules.rule.create.fromStore');
        Session::flash('gaEventCategory', 'rules');
        Session::flash('gaEventAction', 'create-rule');

        return view(
            'rules.rule.create', compact('subTitleIcon', 'oldTriggers', 'oldActions', 'triggerCount', 'actionCount', 'ruleGroup', 'subTitle')
        );
    }

    /**
     * @param Rule $rule
     *
     * @return View
     * @internal param RuleRepositoryInterface $repository
     */
    public function delete(Rule $rule)
    {
        $subTitle = trans('firefly.delete_rule', ['title' => $rule->title]);

        // put previous url in session
        Session::put('rules.rule.delete.url', URL::previous());
        Session::flash('gaEventCategory', 'rules');
        Session::flash('gaEventAction', 'delete-rule');

        return view('rules.rule.delete', compact('rule', 'subTitle'));
    }

    /**
     * @param Rule                    $rule
     * @param RuleRepositoryInterface $repository
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RuleRepositoryInterface $repository, Rule $rule)
    {

        $title = $rule->title;
        $repository->destroy($rule);

        Session::flash('success', trans('firefly.deleted_rule', ['title' => $title]));
        Preferences::mark();


        return redirect(session('rules.rule.delete.url'));
    }

    /**
     * @param RuleRepositoryInterface $repository
     * @param Rule                    $rule
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function down(RuleRepositoryInterface $repository, Rule $rule)
    {
        $repository->moveDown($rule);

        return redirect(route('rules.index'));

    }

    /**
     * @param Rule $rule
     *
     * @return View
     */
    public function edit(Rule $rule)
    {
        // has old input?
        if (Input::old()) {
            $oldTriggers  = $this->getPreviousTriggers();
            $triggerCount = count($oldTriggers);
            $oldActions   = $this->getPreviousActions();
            $actionCount  = count($oldActions);
        } else {
            $oldTriggers  = $this->getCurrentTriggers($rule);
            $triggerCount = count($oldTriggers);
            $oldActions   = $this->getCurrentActions($rule);
            $actionCount  = count($oldActions);
        }

        // get rule trigger for update / store-journal:
        $primaryTrigger = $rule->ruleTriggers()->where('trigger_type', 'user_action')->first()->trigger_value;
        $subTitle       = trans('firefly.edit_rule', ['title' => $rule->title]);

        // put previous url in session if not redirect from store (not "return_to_edit").
        if (session('rules.rule.edit.fromUpdate') !== true) {
            Session::put('rules.rule.edit.url', URL::previous());
        }
        Session::forget('rules.rule.edit.fromUpdate');
        Session::flash('gaEventCategory', 'rules');
        Session::flash('gaEventAction', 'edit-rule');

        return view('rules.rule.edit', compact('rule', 'subTitle', 'primaryTrigger', 'oldTriggers', 'oldActions', 'triggerCount', 'actionCount'));
    }

    /**
     * @return View
     */
    public function index()
    {
        $this->createDefaultRuleGroup();
        $this->createDefaultRule();

        $ruleGroups = Auth::user()
                          ->ruleGroups()
                          ->orderBy('active', 'DESC')
                          ->orderBy('order', 'ASC')
                          ->with(
                              [
                                  'rules'              => function (HasMany $query) {
                                      $query->orderBy('active', 'DESC');
                                      $query->orderBy('order', 'ASC');

                                  },
                                  'rules.ruleTriggers' => function (HasMany $query) {
                                      $query->orderBy('order', 'ASC');
                                  },
                                  'rules.ruleActions'  => function (HasMany $query) {
                                      $query->orderBy('order', 'ASC');
                                  },
                              ]
                          )->get();

        return view('rules.index', compact('ruleGroups'));
    }

    /**
     * @param RuleRepositoryInterface $repository
     * @param Rule                    $rule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderRuleActions(RuleRepositoryInterface $repository, Rule $rule)
    {
        $ids = Input::get('actions');
        if (is_array($ids)) {
            $repository->reorderRuleActions($rule, $ids);
        }

        return Response::json('true');

    }

    /**
     * @param RuleRepositoryInterface $repository
     * @param Rule                    $rule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderRuleTriggers(RuleRepositoryInterface $repository, Rule $rule)
    {
        $ids = Input::get('triggers');
        if (is_array($ids)) {
            $repository->reorderRuleTriggers($rule, $ids);
        }

        return Response::json('true');

    }

    /**
     * @param RuleFormRequest         $request
     * @param RuleRepositoryInterface $repository
     * @param RuleGroup               $ruleGroup
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(RuleFormRequest $request, RuleRepositoryInterface $repository, RuleGroup $ruleGroup)
    {


        // process the rule itself:
        $data = [
            'rule_group_id'       => $ruleGroup->id,
            'title'               => $request->get('title'),
            'user_id'             => Auth::user()->id,
            'trigger'             => $request->get('trigger'),
            'description'         => $request->get('description'),
            'rule-triggers'       => $request->get('rule-trigger'),
            'rule-trigger-values' => $request->get('rule-trigger-value'),
            'rule-trigger-stop'   => $request->get('rule-trigger-stop'),
            'rule-actions'        => $request->get('rule-action'),
            'rule-action-values'  => $request->get('rule-action-value'),
            'rule-action-stop'    => $request->get('rule-action-stop'),
            'stop_processing'     => $request->get('stop_processing'),
        ];

        $rule = $repository->store($data);
        Session::flash('success', trans('firefly.stored_new_rule', ['title' => $rule->title]));
        Preferences::mark();

        if (intval(Input::get('create_another')) === 1) {
            // set value so create routine will not overwrite URL:
            Session::put('rules.rule.create.fromStore', true);

            return redirect(route('rules.rule.create', [$request->input('what')]))->withInput();
        }

        // redirect to previous URL.
        return redirect(session('rules.rule.create.url'));

    }

    /**
     * @param RuleRepositoryInterface $repository
     * @param Rule                    $rule
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function up(RuleRepositoryInterface $repository, Rule $rule)
    {
        $repository->moveUp($rule);

        return redirect(route('rules.index'));

    }

    /**
     * @param RuleRepositoryInterface $repository
     * @param RuleFormRequest         $request
     * @param Rule                    $rule
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(RuleRepositoryInterface $repository, RuleFormRequest $request, Rule $rule)
    {

        // process the rule itself:
        $data = [
            'title'               => $request->get('title'),
            'active'              => intval($request->get('active')) == 1,
            'trigger'             => $request->get('trigger'),
            'description'         => $request->get('description'),
            'rule-triggers'       => $request->get('rule-trigger'),
            'rule-trigger-values' => $request->get('rule-trigger-value'),
            'rule-trigger-stop'   => $request->get('rule-trigger-stop'),
            'rule-actions'        => $request->get('rule-action'),
            'rule-action-values'  => $request->get('rule-action-value'),
            'rule-action-stop'    => $request->get('rule-action-stop'),
            'stop_processing'     => intval($request->get('stop_processing')) == 1,
        ];
        $repository->update($rule, $data);

        Session::flash('success', trans('firefly.updated_rule', ['title' => $rule->title]));
        Preferences::mark();

        if (intval(Input::get('return_to_edit')) === 1) {
            // set value so edit routine will not overwrite URL:
            Session::put('rules.rule.edit.fromUpdate', true);

            return redirect(route('rules.rule.edit', [$rule->id]))->withInput(['return_to_edit' => 1]);
        }

        // redirect to previous URL.
        return redirect(session('rules.rule.edit.url'));
    }
    
    /**
     * @param JournalRepositoryInterface $repository
     * 
     * @return \Illuminate\View\View
     */
    public function testTriggers() {
        /** @var JournalRepositoryInterface $repository */
        $repository = app('FireflyIII\Repositories\Journal\JournalRepositoryInterface');
        
        // Create a list of triggers
        $triggers = $this->getTriggerList();
        
        // We start searching for transactions. For performance reasons, there are limits
        // to the search: a maximum number of results and a maximum number of transactions
        // to search in
        // TODO: Make these values configurable
        $maxResults = 50;
        $maxTransactionsToSearchIn = 1000;
        
        // Find a list of transactions
        $numTransactionsProcessed = 0;
        $page = 1;
        $reachedEndOfList = false;
        $matchingTransactions = [];
        
        // Try to determine an optimal page size
        $pagesize = min($maxTransactionsToSearchIn / 2, $maxResults * 2);
        $transactionTypes = [ TransactionType::DEPOSIT, TransactionType::WITHDRAWAL, TransactionType::TRANSFER ];
        
        do {
            // For now, assume the repository uses a default page size of 50.
            $offset = $page > 0 ? ($page - 1) * 50 : 0;
            $transactions = $repository->getJournalsOfTypes( $transactionTypes,  $offset, $page, $pagesize)->getCollection()->all();
            
            // If less transactions are returned than the pagesize, we reached the end of the list and stop searching
            if(count($transactions) < $pagesize) {
                $reachedEndOfList = true;
            }
            $numTransactionsProcessed += count($transactions);
            
            // Filter transactions that match the rule
            $matchingTransactions += array_filter( $transactions, function($transaction) use($triggers) {
                $processor = new Processor(new Rule, $transaction);
                return $processor->isTriggeredBy($triggers);
            });
            
            // Update counters
            $page++;
        } while( !$reachedEndOfList && count($matchingTransactions) < $maxResults && $numTransactionsProcessed < $maxTransactionsToSearchIn );
        
        // If the list of matchingTransactions is larger than the maximum number of results
        // (e.g. if a large percentage of the transactions match), truncate the list
        $matchingTransactions = array_slice($matchingTransactions, 0, $maxResults);
        
        // Warn the user if only a subset of transactions is returned
        if(count( $matchingTransactions ) == $maxResults) {
            $warning = trans('firefly.warning_transaction_subset', [ 'max_num_transactions' => $maxResults ] );
        } else if(count($matchingTransactions) == 0){
            $warning = trans('firefly.warning_no_matching_transactions', [ 'num_transactions' => $maxTransactionsToSearchIn ] );
        } else {
            $warning = "";
        }
        
        // Return json response
        $view = view('list.journals-tiny', [ 'transactions' => $matchingTransactions ])->render();
        
        return Response::json(['html' => $view, 'warning' => $warning ]);
    }
    
    /**
     * @param Rule $rule
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function executeOnExistingTransactions(Rule $rule) {
        /** @var JournalRepositoryInterface $repository */
        $repository = app('FireflyIII\Repositories\Journal\JournalRepositoryInterface');
    
        // Loop through all transactions
        $page = 1;
        $reachedEndOfList = false;
    
        // Try to determine an optimal page size
        // TODO: Make this pagesize configurable
        $pagesize = 250;
        $transactionTypes = [ TransactionType::DEPOSIT, TransactionType::WITHDRAWAL, TransactionType::TRANSFER ];
        
        // Create a processor object an reuse the object for performance reasons
        $processor = new Processor($rule, new TransactionJournal);
        
        do {
            // For now, assume the repository uses a default page size of 50.
            $offset = $page > 0 ? ($page - 1) * 50 : 0;
            $journals = $repository->getJournalsOfTypes($transactionTypes, $offset, $page, $pagesize)->getCollection()->all();
    
            // If less transactions are returned than the pagesize, we reached the end of the list and stop searching
            if(count($journals) < $pagesize) {
                $reachedEndOfList = true;
            }
    
            // Execute the rule on each transaction.
            foreach($journals as $journal) {
                $processor->setJournal($journal);
                $processor->handle();
            }
    
            // Update counters
            $page++;
        } while( !$reachedEndOfList );
    
        // redirect to previous URL.
        Session::flash('success', trans('firefly.executed_rule_on_existing_transactions', ['title' => $rule->title]));
        return redirect(URL::previous());
    }
    
    /**
     * Returns a list of triggers as provided in the URL
     * @return array
     */
    protected function getTriggerList() {
        $triggers = [];
        $order = 1;
        $data = [
            'rule-triggers'       => Input::get('rule-trigger'),
            'rule-trigger-values' => Input::get('rule-trigger-value'),
            'rule-trigger-stop'   => Input::get('rule-trigger-stop'),
        ];
        
        foreach ($data['rule-triggers'] as $index => $trigger) {
            $value          = $data['rule-trigger-values'][$index];
            $stopProcessing = isset($data['rule-trigger-stop'][$index]) ? true : false;
        
            // Create a new trigger object
            $ruleTrigger = new RuleTrigger;
            $ruleTrigger->order           = $order;
            $ruleTrigger->active          = 1;
            $ruleTrigger->stop_processing = $stopProcessing;
            $ruleTrigger->trigger_type    = $trigger;
            $ruleTrigger->trigger_value   = $value;
            
            // Store in list
            $triggers[] = $ruleTrigger;
            $order++;
        }
        
        return $triggers;
    }
    
    private function createDefaultRule()
    {
        /** @var RuleRepositoryInterface $repository */
        $repository = app('FireflyIII\Repositories\Rule\RuleRepositoryInterface');

        if ($repository->count() === 0) {
            $data = [
                'rule_group_id'       => $repository->getFirstRuleGroup()->id,
                'stop_processing'     => 0,
                'user_id'             => Auth::user()->id,
                'title'               => trans('firefly.default_rule_name'),
                'description'         => trans('firefly.default_rule_description'),
                'trigger'             => 'store-journal',
                'rule-trigger-values' => [
                    trans('firefly.default_rule_trigger_description'),
                    trans('firefly.default_rule_trigger_from_account'),
                ],
                'rule-action-values'  => [
                    trans('firefly.default_rule_action_prepend'),
                    trans('firefly.default_rule_action_set_category'),
                ],

                'rule-triggers' => ['description_is', 'from_account_is'],
                'rule-actions'  => ['prepend_description', 'set_category'],
            ];

            $repository->store($data);
        }

    }

    /**
     *
     */
    private function createDefaultRuleGroup()
    {

        /** @var RuleGroupRepositoryInterface $repository */
        $repository = app('FireflyIII\Repositories\RuleGroup\RuleGroupRepositoryInterface');

        if ($repository->count() === 0) {
            $data = [
                'user_id'        => Auth::user()->id,
                'title'       => trans('firefly.default_rule_group_name'),
                'description' => trans('firefly.default_rule_group_description'),
            ];

            $repository->store($data);
        }
    }

    /**
     * @param Rule $rule
     *
     * @return array
     */
    private function getCurrentActions(Rule $rule)
    {
        $index   = 0;
        $actions = [];

        /** @var RuleAction $entry */
        foreach ($rule->ruleActions as $entry) {
            $count     = ($index + 1);
            $actions[] = view(
                'rules.partials.action',
                [
                    'oldTrigger' => $entry->action_type,
                    'oldValue'   => $entry->action_value,
                    'oldChecked' => $entry->stop_processing,
                    'count'      => $count,
                ]
            )->render();
            $index++;
        }

        return $actions;
    }

    /**
     * @param Rule $rule
     *
     * @return array
     */
    private function getCurrentTriggers(Rule $rule)
    {
        $index    = 0;
        $triggers = [];

        /** @var RuleTrigger $entry */
        foreach ($rule->ruleTriggers as $entry) {
            if ($entry->trigger_type != 'user_action') {
                $count      = ($index + 1);
                $triggers[] = view(
                    'rules.partials.trigger',
                    [
                        'oldTrigger' => $entry->trigger_type,
                        'oldValue'   => $entry->trigger_value,
                        'oldChecked' => $entry->stop_processing,
                        'count'      => $count,
                    ]
                )->render();
                $index++;
            }
        }

        return $triggers;
    }

    /**
     * @return array
     */
    private function getPreviousActions()
    {
        $newIndex = 0;
        $actions  = [];
        /** @var array $oldActions */
        $oldActions = is_array(Input::old('rule-action')) ? Input::old('rule-action') : [];
        foreach ($oldActions as $index => $entry) {
            $count     = ($newIndex + 1);
            $checked   = isset(Input::old('rule-action-stop')[$index]) ? true : false;
            $actions[] = view(
                'rules.partials.action',
                [
                    'oldTrigger' => $entry,
                    'oldValue'   => Input::old('rule-action-value')[$index],
                    'oldChecked' => $checked,
                    'count'      => $count,
                ]
            )->render();
            $newIndex++;
        }

        return $actions;
    }

    /**
     * @return array
     */
    private function getPreviousTriggers()
    {
        $newIndex = 0;
        $triggers = [];
        /** @var array $oldTriggers */
        $oldTriggers = is_array(Input::old('rule-trigger')) ? Input::old('rule-trigger') : [];
        foreach ($oldTriggers as $index => $entry) {
            $count      = ($newIndex + 1);
            $oldChecked = isset(Input::old('rule-trigger-stop')[$index]) ? true : false;
            $triggers[] = view(
                'rules.partials.trigger',
                [
                    'oldTrigger' => $entry,
                    'oldValue'   => Input::old('rule-trigger-value')[$index],
                    'oldChecked' => $oldChecked,
                    'count'      => $count,
                ]
            )->render();
            $newIndex++;
        }

        return $triggers;
    }


}
