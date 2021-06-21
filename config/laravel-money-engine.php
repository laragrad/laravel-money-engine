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
            // 1 => [
            //     'handler' => \App\Laragrad\MoneyEngine\Handlers\OperationExamleHandler::class,
            // ],
        ],
    ],

    'entry' => [
        'compensation_kinds' => [
            Entry::ENTRY_COMPENSATION_KIND_INVERSE => [
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
            Entry::ENTRY_SYS_TYPE_INVERSE => [
                'is_compensation' => true,
            ],
            Entry::ENTRY_SYS_TYPE_STORNO => [
                'is_compensation' => true,
            ],
        ],
        'types' => [
            // 1 => [
            //     'db_account_code' => 'first_entity_sum',
            //     'cr_account_code' => 'second_entity_sum',
            //     'handler' => null,
            // ],
        ],
    ],

    'account' => [
        // 'first_entity_sum' => [
        //     'model' => \App\Models\FirstEntity::class,
        //     'column' => 'sum',
        //     'kind' => AccountableEntityInterface::ACCOUNT_KIND_ACTIVE,
        // ],
        // 'second_entity_sum' => [
        //     'model' => \App\Models\SecondEntity::class,
        //     'column' => 'sum',
        //     'kind' => AccountableEntityInterface::ACCOUNT_KIND_PASSIVE,
        // ],
    ],

    'entity' => [
        // \App\Models\FirstEntity::class => [
        //     'accounts' => [
        //         'first_entity_sum',
        //     ],
        // ],
        // \App\Models\SecondEntity::class => [
        //     'accounts' => [
        //         'second_entity_sum',
        //     ],
        // ],
    ],
];