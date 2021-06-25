<?php

/**
 * Example of money-engine configuration
 */

use Laragrad\MoneyEngine\Models\Entry;
use Laragrad\MoneyEngine\Models\Operation;
use Laragrad\MoneyEngine\Models\Contracts\AccountableEntityInterface;

return [

    'default_currency_code' => env('SYS_CURRENCY_CODE', 'USD'),

    'operation' => [
        'types' => [
//             9999 => [
//                 'handler' => \Laragrad\MoneyEngine\Example\Handlers\ExampleOperationHandler::class,
//                 'validation' => [
//                     'rules' => [
//                         'type_code' => ['integer', 'required'],
//                         'details' => ['array', 'required'],
//                         'details.bank_payment_id' => ['uuid', 'required'],
//                         'details.bill_id' => ['uuid', 'required'],
//                         'details.sum' => ['numeric', 'required'],
//                     ],
//                 ],
//             ],
        ],
    ],

    'entry' => [
        'compensation_kinds' => [
            Entry::ENTRY_COMPENSATION_KIND_REVERSE => [
                //
            ],
            Entry::ENTRY_COMPENSATION_KIND_STORNO => [
                //
            ],
        ],
        'sys_types' => [
            Entry::ENTRY_SYS_TYPE_NORMAL => [
                'is_compensation' => false,
            ],
            Entry::ENTRY_SYS_TYPE_REVERSE => [
                'is_compensation' => true,
            ],
            Entry::ENTRY_SYS_TYPE_STORNO => [
                'is_compensation' => true,
            ],
        ],
        'types' => [
//             9999 => [
//                 'db_account_code' => 'bank_pay_rest_sum',
//                 'cr_account_code' => 'bill_payed_sum',
//                 'handler' => null,
//             ],
        ],
    ],

    /**
     * Account types
     */
    'account' => [
//         'bank_pay_rest_sum' => [
//             'entity' => \Laragrad\MoneyEngine\Example\Models\TmpBankPayment::class,
//             'column' => 'rest_sum',
//             'kind' => AccountableEntityInterface::ACCOUNT_KIND_PASSIVE,
//         ],
//         'bill_payed_sum' => [
//             'entity' => \Laragrad\MoneyEngine\Example\Models\TmpBill::class,
//             'column' => 'paid_sum',
//             'kind' => AccountableEntityInterface::ACCOUNT_KIND_PASSIVE,
//         ],
    ],

    /**
     * Accountable entities
     * -- There is the list of entities that has an account attributes
     */
    'entities' => [
//         \Laragrad\MoneyEngine\Example\Models\TmpBankPayment::class => [
//             'accounts' => [
//                 'bank_pay_rest_sum',
//             ],
//         ],
//         \Laragrad\MoneyEngine\Example\Models\TmpBill::class => [
//             'accounts' => [
//                 'bill_payed_sum',
//             ],
//         ],
    ],
];