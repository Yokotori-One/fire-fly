<?php

/**
 * validation.php
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

/*
 * PLEASE DO NOT EDIT THIS FILE DIRECTLY.
 * YOUR CHANGES WILL BE OVERWRITTEN!
 * YOUR PR WITH CHANGES TO THIS FILE WILL BE REJECTED!
 *
 * GO TO CROWDIN TO FIX OR CHANGE TRANSLATIONS!
 *
 * https://crowdin.com/project/firefly-iii
 *
 */

declare(strict_types=1);

return [
    'missing_where'                   => '배열에 "where"절이 없습니다',
    'missing_update'                  => '배열에 "update"절이 없습니다',
    'invalid_where_key'               => 'JSON의 "where" 절에 유효하지 않은 키가 포함되어 있습니다',
    'invalid_update_key'              => 'JSON의 "update" 절에 유효하지 않은 키가 포함되어 있습니다',
    'invalid_query_data'              => '쿼리의 %s:%s 항목에 잘못된 데이터가 있습니다.',
    'invalid_query_account_type'      => '쿼리에 허용되지 않는 다른 유형의 계정이 포함되어 있습니다.',
    'invalid_query_currency'          => '쿼리에 허용되지 않는 다른 통화 설정이 있는 계정이 포함되어 있습니다.',
    'iban'                            => '유효한 IBAN이 아닙니다.',
    'zero_or_more'                    => '값은 음수가 될 수 없습니다.',
    'more_than_zero'                  => 'The value must be more than zero.',
    'no_asset_account'                => 'This is not an asset account.',
    'date_or_time'                    => '유효한 날짜 또는 시간 값(ISO 8601) 이어야 합니다.',
    'source_equals_destination'       => '소스 계정이 대상 계정과 같습니다.',
    'unique_account_number_for_user'  => '이 계좌 번호는 이미 사용 중인 것 같습니다.',
    'unique_iban_for_user'            => '이 IBAN은 이미 사용 중인 것 같습니다.',
    'reconciled_forbidden_field'      => 'This transaction is already reconciled, you cannot change the ":field"',
    'deleted_user'                    => '보안 제약으로 인해 이 이메일 주소를 사용하여 등록할 수 없습니다.',
    'rule_trigger_value'              => '선택한 트리거에 대해 이 값은 유효하지 않습니다.',
    'rule_action_value'               => '선택한 액션에 대해 이 값은 유효하지 않습니다.',
    'file_already_attached'           => '업로드된 파일 ":name"이 이 개체에 이미 첨부되어 있습니다.',
    'file_attached'                   => '":name" 파일을 성공적으로 업로드했습니다.',
    'must_exist'                      => ':attribute 필드의 ID가 데이터베이스에 존재하지 않습니다.',
    'all_accounts_equal'              => '이 필드의 모든 계정은 동일해야 합니다.',
    'group_title_mandatory'           => '거래가 두 개 이상일 경우 그룹 제목은 필수입니다.',
    'transaction_types_equal'         => '모든 분할은 동일한 유형이어야 합니다.',
    'invalid_transaction_type'        => '잘못된 거래 유형입니다.',
    'invalid_selection'               => '선택이 잘못되었습니다.',
    'belongs_user'                    => 'This value is linked to an object that does not seem to exist.',
    'belongs_user_or_user_group'      => 'This value is linked to an object that does not seem to exist in your current financial administration.',
    'at_least_one_transaction'        => '하나 이상의 거래가 필요합니다.',
    'recurring_transaction_id'        => '하나 이상의 거래가 필요합니다.',
    'need_id_to_match'                => 'API가 일치시킬수 있도록 이 엔트리를 ID와 함께 제출해야 합니다.',
    'too_many_unmatched'              => '제출된 거래가 각각의 데이터베이스 엔트리와 일치하지 않습니다. 기존 엔트리에 유효한 ID가 있는지 확인해 주세요.',
    'id_does_not_match'               => '입력된 ID #:id가 예상된 ID와 일치하지 않습니다. 일치시키거나 빈칸을 입력하십시오.',
    'at_least_one_repetition'         => '하나 이상의 반복이 필요합니다.',
    'require_repeat_until'            => '반복 횟수 또는 종료 날짜(repeat_until) 가 필요합니다. 둘 다 없습니다.',
    'require_currency_info'           => '이 필드의 내용은 통화 정보가 없으면 유효하지 않습니다.',
    'not_transfer_account'            => '이 계정은 이체에 사용할 수 있는 계정이 아닙니다.',
    'require_currency_amount'         => '이 필드의 내용은 외화 수량 정보가 없으면 유효하지 않습니다.',
    'require_foreign_currency'        => '이 항목은 숫자가 필요합니다.',
    'require_foreign_dest'            => '이 항목 값은 대상 계정의 통화와 일치해야 합니다.',
    'require_foreign_src'             => '이 항목 값은 소스 계정의 통화와 일치해야 합니다.',
    'equal_description'               => '거래 설명은 전역 설명과 같지 않아야 합니다.',
    'file_invalid_mime'               => '":name" 파일은 새로운 업로드를 허용하지 않는 ":mime" 타입입니다.',
    'file_too_large'                  => '":name" 파일이 너무 큽니다.',
    'belongs_to_user'                 => '":attribute" 의 값을 알 수 없습니다.',
    'accepted'                        => '":attribute" 을(를) 수락해야 합니다.',
    'bic'                             => '유효한 BIC가 아닙니다.',
    'at_least_one_trigger'            => '규칙은 적어도 하나의 트리거를 가져야 합니다.',
    'at_least_one_active_trigger'     => '규칙은 적어도 하나의 활성화된 트리거를 가져야 합니다.',
    'at_least_one_action'             => '규칙은 적어도 하나의 액션을 가져야 합니다.',
    'at_least_one_active_action'      => '규칙은 적어도 하나의 활성화된 액션을 가져야 합니다.',
    'base64'                          => '유효한 base64 인코딩 데이터가 아닙니다.',
    'model_id_invalid'                => '제공된 ID가 이 모델에 유효하지 않은 것 같습니다.',
    'less'                            => ':attribute 은(는) 10,000,000 보다 작아야 합니다.',
    'active_url'                      => ':attribute 은(는) 유효한 URL이 아닙니다.',
    'after'                           => ':attribute는 :date 이후의 날짜여야 합니다.',
    'date_after'                      => '시작 날짜는 종료 날짜 이전이어야 합니다.',
    'alpha'                           => ':attribute은(는) 문자만 포함할 수 있습니다.',
    'alpha_dash'                      => ':attribute은(는) 문자, 숫자, 대쉬(-)만 포함할 수 있습니다.',
    'alpha_num'                       => ':attribute은(는) 문자와 숫자만 포함할 수 있습니다.',
    'array'                           => ':attribute은(는) 배열이어야 합니다.',
    'unique_for_user'                 => '이 :attribute은(는) 이미 항목에 있습니다.',
    'before'                          => ':attribute은(는) :date 이전의 날짜여야 합니다.',
    'unique_object_for_user'          => '이 이름은 이미 사용 중입니다.',
    'unique_account_for_user'         => '이 계정명은 이미 사용중입니다.',

    /*
 * PLEASE DO NOT EDIT THIS FILE DIRECTLY.
 * YOUR CHANGES WILL BE OVERWRITTEN!
 * YOUR PR WITH CHANGES TO THIS FILE WILL BE REJECTED!
 *
 * GO TO CROWDIN TO FIX OR CHANGE TRANSLATIONS!
 *
 * https://crowdin.com/project/firefly-iii
 *
 */

    'between.numeric'                 => ':attribute은(는) :min과 :max 사이의 값이어야 합니다.',
    'between.file'                    => ':attribute은(는) :min에서 :max 킬로바이트 사이여야 합니다.',
    'between.string'                  => ':attribute은(는) 최소 :min 최대 :max 자 여야 합니다.',
    'between.array'                   => ':attribute은(는) :min에서 :max 개의 항목이 있어야 합니다.',
    'boolean'                         => ':attribute은(는) true 혹은 false 여야 합니다.',
    'confirmed'                       => ':attribute 확인이 일치하지 않습니다.',
    'date'                            => ':attribute이(가) 유효한 날짜가 아닙니다.',
    'date_format'                     => ':attribute이(가) :format 형식과 일치하지 않습니다.',
    'different'                       => ':attribute와(과) :other을(를) 다르게 구성하세요.',
    'digits'                          => ':attribute은(는) :digits 자리 숫자여야 합니다.',
    'digits_between'                  => ':attribute은(는) :min에서 :max 자리 숫자여야 합니다.',
    'email'                           => ':attribute은(는) 유효한 이메일 주소여야 합니다.',
    'filled'                          => ':attribute 항목은 필수입니다.',
    'exists'                          => '선택한 :attribute이(가) 올바르지 않습니다.',
    'image'                           => ':attribute은(는) 이미지여야 합니다.',
    'in'                              => '선택한 :attribute이(가) 올바르지 않습니다.',
    'integer'                         => ':attribute은(는) 정수여야 합니다.',
    'ip'                              => ':attribute은(는) 유효한 IP 주소여야 합니다.',
    'json'                            => ':attribute은(는) 올바른 JSON 값이어야 합니다.',
    'max.numeric'                     => ':attribute은(는) :max 보다 클 수 없습니다.',
    'max.file'                        => ':attribute은(는) :max 킬로바이트 보다 작아야 합니다.',
    'max.string'                      => ':attribute 는 :max 자보다 작아야 합니다.',
    'max.array'                       => ':attribute은(는) :max 개보다 작아야 합니다.',
    'mimes'                           => ':attribute은(는) :values 파일 타입이어야 합니다.',
    'min.numeric'                     => ':attribute은(는) :min 보다 커야 합니다.',
    'lte.numeric'                     => ':attribute은(는) :value보다 작거나 같아야 합니다.',
    'min.file'                        => ':attribute은(는) :min 킬로바이트 이상이어야 합니다.',
    'min.string'                      => ':attribute은(는) :min 자 이상이어야 합니다.',
    'min.array'                       => ':attribute은(는) :min 개 이상이어야 합니다.',
    'not_in'                          => '선택한 :attribute이(가) 올바르지 않습니다.',
    'numeric'                         => ':attribute은(는) 숫자여야 합니다.',
    'scientific_notation'             => 'The :attribute cannot use the scientific notation.',
    'numeric_native'                  => '기본 금액은 숫자여야 합니다.',
    'numeric_destination'             => '대상 금액은 숫자여야 합니다.',
    'numeric_source'                  => '소스 금액은 숫자여야 합니다.',
    'regex'                           => ':attribute의 형식이 올바르지 않습니다.',
    'required'                        => ':attribute 항목은 필수입니다.',
    'required_if'                     => ':other이(가) :value 일때 :attribute 항목은 필수입니다.',
    'required_unless'                 => ':other이(가) :values가 없는 경우 :attribute 항목은 필수입니다.',
    'required_with'                   => ':values이(가) 있을 경우 :attribute 항목은 필수입니다.',
    'required_with_all'               => ':values이(가) 있을 경우 :attribute 항목은 필수입니다.',
    'required_without'                => ':values가 없는 경우 :attribute 필드는 필수입니다.',
    'required_without_all'            => ':values(이)가 모두 없을 때 :attribute 항목은 필수입니다.',
    'same'                            => ':attribute와(과) :other은(는) 일치해야 합니다.',
    'size.numeric'                    => ':attribute은(는) :size 크기여야 합니다.',
    'amount_min_over_max'             => '최소 금액은 최대 금액보다 클 수 없습니다.',
    'size.file'                       => ':attribute은(는) :size 킬로바이트여야 합니다.',
    'size.string'                     => ':attribute은(는) :size 자여야 합니다.',
    'size.array'                      => ':attribute은(는) :size 개의 항목을 포함해야 합니다.',
    'unique'                          => ':attribute은(는) 이미 사용중 입니다.',
    'string'                          => ':attribute은(는) 문자열이어야 합니다.',
    'url'                             => ':attribute의 형식이 올바르지 않습니다.',
    'timezone'                        => ':attribute은(는) 유효한 시간대이어야 합니다.',
    '2fa_code'                        => ':attribute 항목이 올바르지 않습니다.',
    'dimensions'                      => ':attribute의 이미지 크기가 올바르지 않습니다.',
    'distinct'                        => ':attribute 항목이 중복된 값을 갖고있습니다.',
    'file'                            => ':attribute은(는) 파일이어야 합니다.',
    'in_array'                        => ':other 에 :attribute 항목이 존재하지 않습니다.',
    'present'                         => ':attribute 항목은 필수입니다.',
    'amount_zero'                     => '총합은 0이 될 수 없습니다.',
    'current_target_amount'           => '현재 금액은 목표 금액보다 적어야 합니다.',
    'unique_piggy_bank_for_user'      => '저금통의 이름은 고유해야 합니다.',
    'unique_object_group'             => '그룸명은 고유해야 합니다',
    'starts_with'                     => '값은 :values로 시작해야 합니다.',
    'unique_webhook'                  => 'URL, 트리거, 응답 및 전달의 조합으로 구성된 웹훅이 이미 존재합니다.',
    'unique_existing_webhook'         => 'URL, 트리거, 응답 및 전달의 조합으로 구성된 다른 웹훅이 이미 존재합니다.',
    'same_account_type'               => '두 계정은 동일한 계정 유형이어야 합니다.',
    'same_account_currency'           => '두 계정의 통화 설정이 동일해야 합니다.',

    /*
 * PLEASE DO NOT EDIT THIS FILE DIRECTLY.
 * YOUR CHANGES WILL BE OVERWRITTEN!
 * YOUR PR WITH CHANGES TO THIS FILE WILL BE REJECTED!
 *
 * GO TO CROWDIN TO FIX OR CHANGE TRANSLATIONS!
 *
 * https://crowdin.com/project/firefly-iii
 *
 */

    'secure_password'                 => "안전한 비밀번호가 아닙니다. 다시 시도해 주세요. 자세한 내용은 https://bit.ly/FF3-password-security 를 \u{200b}\u{200b}방문하세요.",
    'valid_recurrence_rep_type'       => '반복 거래에 대한 반복 유형이 잘못되었습니다.',
    'valid_recurrence_rep_moment'     => '이 유형의 반복에 대한 반복 시점이 잘못되었습니다.',
    'invalid_account_info'            => '잘못된 계정 정보입니다.',
    'attributes'                      => [
        'email'                   => '이메일 주소',
        'description'             => '상세정보',
        'amount'                  => '금액',
        'transactions.*.amount'   => '거래 금액',
        'name'                    => '이름',
        'piggy_bank_id'           => '저금통 ID',
        'targetamount'            => '목표 금액',
        'opening_balance_date'    => '개설일',
        'opening_balance'         => '초기 잔고',
        'match'                   => '매치',
        'amount_min'              => '최소 금액',
        'amount_max'              => '최대 금액',
        'title'                   => '제목',
        'tag'                     => '태그',
        'transaction_description' => '거래 상세내역',
        'rule-action-value.1'     => '규칙 액션 값 #1',
        'rule-action-value.2'     => '규칙 액션 값 #2',
        'rule-action-value.3'     => '규칙 액션 값 #3',
        'rule-action-value.4'     => '규칙 액션 값 #4',
        'rule-action-value.5'     => '규칙 액션 값 #5',
        'rule-action.1'           => '규칙 액션 #1',
        'rule-action.2'           => '규칙 액션 #2',
        'rule-action.3'           => '규칙 액션 #3',
        'rule-action.4'           => '규칙 액션 #4',
        'rule-action.5'           => '규칙 액션 #5',
        'rule-trigger-value.1'    => '규칙 트리거 값 #1',
        'rule-trigger-value.2'    => '규칙 트리거 값 #2',
        'rule-trigger-value.3'    => '규칙 트리거 값 #3',
        'rule-trigger-value.4'    => '규칙 트리거 값 #4',
        'rule-trigger-value.5'    => '규칙 트리거 값 #5',
        'rule-trigger.1'          => '규칙 트리거 #1',
        'rule-trigger.2'          => '규칙 트리거 #2',
        'rule-trigger.3'          => '규칙 트리거 #3',
        'rule-trigger.4'          => '규칙 트리거 #4',
        'rule-trigger.5'          => '규칙 트리거 #5',
    ],

    // validation of accounts:
    'withdrawal_source_need_data'     => '계속하려면 유효한 소스 계정 ID 및/또는 유효한 소스 계정 이름이 필요합니다.',
    'withdrawal_source_bad_data'      => '[a] ID ":id" 또는 이름 ":name"을 검색할 때 유효한 소스 계정을 찾을 수 없습니다.',
    'withdrawal_dest_need_data'       => '[a] 계속하려면 유효한 대상 계정 ID 및/또는 유효한 대상 계정 이름이 필요합니다.',
    'withdrawal_dest_bad_data'        => 'ID ":id" 또는 이름 ":name"을 검색할 때 유효한 대상 계정을 찾을 수 없습니다.',

    'withdrawal_dest_iban_exists'     => '대상 계정의 IBAN이 이미 자산 계정에 사용되고 있거나, 부채는 출금 대상으로 사용될 수 없습니다.',
    'deposit_src_iban_exists'         => '소스 계정의 IBAN이 이미 자산 계정에 사용되고 있거나, 부채는 입금 소스로 사용될 수 없습니다.',

    'reconciliation_source_bad_data'  => 'ID ":id" 또는 이름 ":name"을 검색할 때 유효한 조정 계정을 찾을 수 없습니다.',

    'generic_source_bad_data'         => '[e] ID ":id" 또는 이름 ":name"을 검색할 때 유효한 소스 계정을 찾을 수 없습니다.',

    'deposit_source_need_data'        => '계속하려면 유효한 소스 계정 ID 및/또는 유효한 소스 계정 이름이 필요합니다.',
    'deposit_source_bad_data'         => '[b] ID ":id" 또는 이름 ":name"을 검색할 때 유효한 소스 계정을 찾을 수 없습니다.',
    'deposit_dest_need_data'          => '[b] 계속하려면 유효한 대상 계정 ID 및/또는 유효한 대상 계정 이름이 필요합니다.',
    'deposit_dest_bad_data'           => 'ID ":id" 또는 이름 ":name"을 검색할 때 유효한 대상 계정을 찾을 수 없습니다.',
    'deposit_dest_wrong_type'         => '제출된 대상 계정이 올바른 유형이 아닙니다.',

    /*
 * PLEASE DO NOT EDIT THIS FILE DIRECTLY.
 * YOUR CHANGES WILL BE OVERWRITTEN!
 * YOUR PR WITH CHANGES TO THIS FILE WILL BE REJECTED!
 *
 * GO TO CROWDIN TO FIX OR CHANGE TRANSLATIONS!
 *
 * https://crowdin.com/project/firefly-iii
 *
 */

    'transfer_source_need_data'       => '계속하려면 유효한 소스 계정 ID 및/또는 유효한 소스 계정 이름이 필요합니다.',
    'transfer_source_bad_data'        => '[c] ID ":id" 또는 이름 ":name"을 검색할 때 유효한 소스 계정을 찾을 수 없습니다.',
    'transfer_dest_need_data'         => '[c] 계속하려면 유효한 대상 계정 ID 및/또는 유효한 대상 계정 이름이 필요합니다.',
    'transfer_dest_bad_data'          => 'ID ":id" 또는 이름 ":name"을 검색할 때 유효한 대상 계정을 찾을 수 없습니다.',
    'need_id_in_edit'                 => "각 분할에는 transaction_journal_id(유효한 \u{200b}\u{200b}ID 또는 0) 가 있어야 합니다.",

    'ob_source_need_data'             => '계속하려면 유효한 소스 계정 ID 및/또는 유효한 소스 계정 이름이 필요합니다.',
    'lc_source_need_data'             => '계속하려면 유효한 소스 계정 ID가 필요합니다.',
    'ob_dest_need_data'               => '[d] 계속하려면 유효한 대상 계정 ID 및/또는 유효한 대상 계정 이름이 필요합니다.',
    'ob_dest_bad_data'                => 'ID ":id" 또는 이름 ":name"을 검색할 때 유효한 대상 계정을 찾을 수 없습니다.',
    'reconciliation_either_account'   => '조정을 제출하려면 소스 계정 또는 대상 계정 중 하나를 제출해야 합니다.',

    'generic_invalid_source'          => '이 계정을 소스 계정으로 사용할 수 없습니다.',
    'generic_invalid_destination'     => '이 계정을 대상 계정으로 사용할 수 없습니다.',

    'generic_no_source'               => '소스 계정 정보를 제출하거나 거래 저널 ID를 제출해야 합니다.',
    'generic_no_destination'          => '대상 계정 정보를 제출하거나 거래 저널 ID를 제출해야 합니다.',

    'gte.numeric'                     => ':attribute의 값은 :value 이상이어야 합니다.',
    'gt.numeric'                      => ':attribute의 값은 :value보다 커야 합니다.',
    'gte.file'                        => ':attribute의 크기는 :value 킬로바이트 이상이어야 합니다.',
    'gte.string'                      => ':attribute은(는) :value 자 이상이어야 합니다.',
    'gte.array'                       => ':attribute은(는) :value개 이상이어야합니다.',

    'amount_required_for_auto_budget' => '금액을 입력하세요.',
    'auto_budget_amount_positive'     => '금액은 0보다 커야 합니다.',
    'auto_budget_period_mandatory'    => '자동 예산 기간은 필수 항목입니다.',

    // no access to administration:
    'no_access_user_group'            => '이 관리에 대한 올바른 액세스 권한이 없습니다.',
];

/*
 * PLEASE DO NOT EDIT THIS FILE DIRECTLY.
 * YOUR CHANGES WILL BE OVERWRITTEN!
 * YOUR PR WITH CHANGES TO THIS FILE WILL BE REJECTED!
 *
 * GO TO CROWDIN TO FIX OR CHANGE TRANSLATIONS!
 *
 * https://crowdin.com/project/firefly-iii
 *
 */
