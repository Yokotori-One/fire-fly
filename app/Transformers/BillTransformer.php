<?php
/**
 * BillTransformer.php
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Transformers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use FireflyIII\Models\Bill;
use FireflyIII\Models\ObjectGroup;
use FireflyIII\Models\TransactionJournal;
use FireflyIII\Repositories\Bill\BillRepositoryInterface;
use Illuminate\Support\Collection;
use Log;

/**
 * Class BillTransformer
 */
class BillTransformer extends AbstractTransformer
{
    private BillRepositoryInterface $repository;

    /**
     * BillTransformer constructor.
     *
     
     */
    public function __construct()
    {
        $this->repository = app(BillRepositoryInterface::class);
    }

    /**
     * Transform the bill.
     *
     * @param Bill $bill
     *
     * @return array
     */
    public function transform(Bill $bill): array
    {
        $paidData = $this->paidData($bill);
        $payDates = $this->payDates($bill);
        $currency = $bill->transactionCurrency;
        $notes    = $this->repository->getNoteText($bill);
        $notes    = '' === $notes ? null : $notes;
        $this->repository->setUser($bill->user);

        $objectGroupId    = null;
        $objectGroupOrder = null;
        $objectGroupTitle = null;
        /** @var ObjectGroup $objectGroup */
        $objectGroup = $bill->objectGroups->first();
        if (null !== $objectGroup) {
            $objectGroupId    = (int)$objectGroup->id;
            $objectGroupOrder = (int)$objectGroup->order;
            $objectGroupTitle = $objectGroup->title;
        }

        $paidDataFormatted = [];
        $payDatesFormatted = [];
        foreach ($paidData['paid_dates'] as $object) {
            $object['date']      = Carbon::createFromFormat('!Y-m-d', $object['date'], config('app.timezone'))->toAtomString();
            $paidDataFormatted[] = $object;
        }

        foreach ($payDates as $string) {
            $payDatesFormatted[] = Carbon::createFromFormat('!Y-m-d', $string, config('app.timezone'))->toAtomString();
        }
        $nextExpectedMatch = null;
        if (null !== $paidData['next_expected_match']) {
            $nextExpectedMatch = Carbon::createFromFormat('!Y-m-d', $paidData['next_expected_match'], config('app.timezone'))->toAtomString();
        }
        $nextExpectedMatchDiff = trans('firefly.not_expected_period');
        // converting back and forth is bad code but OK.
        $temp = new Carbon($nextExpectedMatch);
        if ($temp->isToday()) {
            $nextExpectedMatchDiff = trans('firefly.today');
        }

        $current = $payDatesFormatted[0] ?? null;
        if (null !== $current && !$temp->isToday()) {
            $temp2                 = Carbon::createFromFormat('Y-m-d\TH:i:sP', $current);
            $nextExpectedMatchDiff = $temp2->diffForHumans(today(config('app.timezone')), CarbonInterface::DIFF_RELATIVE_TO_NOW);
        }
        unset($temp, $temp2);

        return [
            'id'                       => (int)$bill->id,
            'created_at'               => $bill->created_at->toAtomString(),
            'updated_at'               => $bill->updated_at->toAtomString(),
            'currency_id'              => (string)$bill->transaction_currency_id,
            'currency_code'            => $currency->code,
            'currency_symbol'          => $currency->symbol,
            'currency_decimal_places'  => (int)$currency->decimal_places,
            'name'                     => $bill->name,
            'amount_min'               => app('steam')->bcround($bill->amount_min, $currency->decimal_places),
            'amount_max'               => app('steam')->bcround($bill->amount_max, $currency->decimal_places),
            'date'                     => $bill->date->toAtomString(),
            'end_date'                 => $bill->end_date?->toAtomString(),
            'extension_date'           => $bill->extension_date?->toAtomString(),
            'repeat_freq'              => $bill->repeat_freq,
            'skip'                     => (int)$bill->skip,
            'active'                   => $bill->active,
            'order'                    => (int)$bill->order,
            'notes'                    => $notes,
            'object_group_id'          => $objectGroupId ? (string)$objectGroupId : null,
            'object_group_order'       => $objectGroupOrder,
            'object_group_title'       => $objectGroupTitle,

            // these fields need work:
            'next_expected_match'      => $nextExpectedMatch,
            'next_expected_match_diff' => $nextExpectedMatchDiff,
            'pay_dates'                => $payDatesFormatted,
            'paid_dates'               => $paidDataFormatted,
            'links'                    => [
                [
                    'rel' => 'self',
                    'uri' => '/bills/' . $bill->id,
                ],
            ],
        ];
    }

    /**
     * Get the data the bill was paid and predict the next expected match.
     *
     * @param Bill $bill
     *
     * @return array
     */
    protected function paidData(Bill $bill): array
    {
        Log::debug(sprintf('Now in paidData for bill #%d', $bill->id));
        if (null === $this->parameters->get('start') || null === $this->parameters->get('end')) {
            Log::debug('parameters are NULL, return empty array');

            return [
                'paid_dates'          => [],
                'next_expected_match' => null,
            ];
        }
        /** @var Carbon $start */
        $start = clone $this->parameters->get('start');
        
        /*
         *  Get from database when bill was paid.
         */
        $set = $this->repository->getPaidDatesInRange($bill, $start, $this->parameters->get('end'));
        //Log::debug(sprintf('Count %d entries in getPaidDatesInRange()', $set->count()));

        /*
         * Grab from array the most recent payment. If none exist, fall back to the start date and pretend *that* was the last paid date.
         */
        //Log::debug(sprintf('Grab last paid date from function, return %s if it comes up with nothing.', $start->format('Y-m-d H:i')));
        $lastPaidDate = $this->lastPaidDate($set, $start);
        //Log::debug(sprintf('Result of lastPaidDate is %s', $lastPaidDate->format('Y-m-d H:i')));

        /*
         * The next expected match (nextMatch) is, initially, the bill's date.
         */
        $nextMatch = clone $bill->date;
        $originalNextMatch = clone $bill->date;
        //Log::debug(sprintf('Next match is %s ($bill->date)', $nextMatch->format('Y-m-d H:i')));
        $skip = 0;
        while ($nextMatch < $lastPaidDate) {
            /*
             * As long as this date is smaller than the last time the bill was paid, keep jumping ahead.
             * For example: 1 jan, 1 feb, etc.
             */
            //Log::debug(sprintf('next match %s < last paid date %s, so add one period.', $nextMatch->format('Y-m-d H:i'), $lastPaidDate->format('Y-m-d H:i')));
            //Log::debug(sprintf("Next match addPeriod: originalNextMatch:%s, repeat:%s, bill->skip:%s, skip:%s", $originalNextMatch, $bill->repeat_freq, $bill->skip, $skip ));
            if ($bill->skip > 0) {
                $skip = $skip + $bill->skip;
            }
            $nextMatch = app('navigation')->addPeriod($originalNextMatch, $bill->repeat_freq, $skip);
            $skip++;
            //Log::debug(sprintf('Next match is now %s', $nextMatch->format('Y-m-d H:i')));
        }
        
        if ($nextMatch->isSameDay($lastPaidDate) && !$nextMatch->isSameDay($start)) {
            /*
             * Add another period because it's the same day as the last paid date, but
             * only if its not the day we started (default date) aka the bill was not payed yet.
             */
            //Log::debug('Because the last paid date was on the same day as our next expected match, add another day.');
            $nextMatch = app('navigation')->addPeriod($nextMatch, $bill->repeat_freq, $bill->skip);
        }
        /*
         * At this point the "next match" is exactly after the last time the bill was paid.
         */
        $result = [];
        foreach ($set as $entry) {
            $result[] = [
                'transaction_group_id'   => (int)$entry->transaction_group_id,
                'transaction_journal_id' => (int)$entry->id,
                'date'                   => $entry->date->format('Y-m-d'),
            ];
        }

        //Log::debug('Result', $result);

        return [
            'paid_dates'          => $result,
            'next_expected_match' => $nextMatch->format('Y-m-d'),
        ];
    }

    /**
     * Returns the latest date in the set, or start when set is empty.
     *
     * @param Collection $dates
     * @param Carbon     $default
     *
     * @return Carbon
     */
    protected function lastPaidDate(Collection $dates, Carbon $default): Carbon
    {
        if (0 === $dates->count()) {
            return $default;
        }
        $latest = $dates->first()->date;
        /** @var TransactionJournal $journal */
        foreach ($dates as $journal) {
            if ($journal->date->gte($latest)) {
                $latest = $journal->date;
            }
        }

        return $latest;
    }

    /**
     * @param Bill $bill
     *
     * @return array
     */
    protected function payDates(Bill $bill): array
    {
        //Log::debug(sprintf('Now in payDates() for bill #%d', $bill->id));
        if (null === $this->parameters->get('start') || null === $this->parameters->get('end')) {
            //Log::debug('No start or end date, give empty array.');

            return [];
        }
        $set          = new Collection();
        $currentStart = clone $this->parameters->get('start');

        $loop = 0;
        while ($currentStart <= $this->parameters->get('end')) {
            //Log::debug(sprintf('currentStart:%s <= end:%s', $currentStart, $this->parameters->get('end')));

            $nextExpectedMatch = $this->nextDateMatch($bill, $currentStart);
            //Log::debug(sprintf('nextExpectedMatch:%s', $nextExpectedMatch));

            // If nextExpectedMatch is after end, we continue:
            if ($nextExpectedMatch > $this->parameters->get('end')) {
                break;
            }
            // add to set
            $set->push(clone $nextExpectedMatch);
            $nextExpectedMatch->addDay();
            $currentStart = clone $nextExpectedMatch;
            $loop++;
            if ($loop > 4) {
                break;
            }
        }
        $simple = $set->map(
            static function (Carbon $date) {
                return $date->format('Y-m-d');
            }
        );
        
        return $simple->toArray();
    }

    /**
     * Given a bill and a date, this method will tell you at which moment this bill expects its next
     * transaction. Whether or not it is there already, is not relevant.
     *
     * @param Bill   $bill
     * @param Carbon $date
     *
     * @return Carbon
     */
    protected function nextDateMatch(Bill $bill, Carbon $date): Carbon
    {
        //Log::debug(sprintf('Now in nextDateMatch(%d, %s)', $bill->id, $date->format('Y-m-d H:i')));
        $start = clone $bill->date;
        //Log::debug(sprintf('Bill start date is %s', $start->format('Y-m-d H:i')));
        $next = clone $bill->date;
        $skip = 0;
        while ($next < $date) {
            if ($bill->skip > 0) {
                $skip = $skip + $bill->skip;
            }
            //Log::debug(sprintf("next: addPeriod: start:%s, repeat:%s, bill->skip:%s, skip:%s", $start, $bill->repeat_freq, $bill->skip, $skip ));
            $next = app('navigation')->addPeriod($start, $bill->repeat_freq, $skip);
            //Log::debug(sprintf("next:%s", $next));
            $skip++;
        }
        //Log::debug(sprintf('Enad of loop, bill start date is now %s', $next->format('Y-m-d H:i')));

        return $next;
    }
}
