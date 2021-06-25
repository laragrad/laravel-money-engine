<?php

use Laragrad\MoneyEngine\Models\Entry;

return [

    'operation' => [
        'types' => [
            // TODO
        ],
    ],

    'entry' => [
        'sys_types' => [
            Entry::ENTRY_SYS_TYPE_NORMAL => 'Normal',
            Entry::ENTRY_SYS_TYPE_STORNO => 'Storno',
            Entry::ENTRY_SYS_TYPE_REVERSE => 'Reverse',
        ],
        
        'types' => [
            // TODO
        ],
        
        'compentstion_kinds' => [
            Entry::ENTRY_COMPENSATION_KIND_STORNO => 'Compensated by storno entry',
            Entry::ENTRY_COMPENSATION_KIND_REVERSE => 'Compensated by reverse entry',
        ],
    ],

    'account' => [
        // TODO
    ],

    'entity' => [
        // TODO
    ],

    'errors' => [
        'account_type_is_incorrect' => 'Account type :type is unavailable for entity :entity',
        'entry_has_been_already_compensated' => 'Entry deleting error. The entry has been already compensated',
        'compensation_entry_deleting_forbidden' => 'Entry deleting error. The compensation entry cannot be compensated',
        'transaction_must_be_started' => 'Transaction must be started',
        'active_account_rest_greater_zero' => 'Active account rest cannot be great than 0',
        'passive_account_rest_less_zero' => 'Passive account rest cannot be less than 0',
        'operation_type_handler_is_undefined' => 'Operation handler for type :type is undefined',
        'operation_type_handler_class_is_not_exists' => 'Operation handler class \':class\' for type :type is not exists',
        'entry_config_is_empty' => 'Entry configuration is empty',
        'operation_config_is_empty' => 'Operation configuration is empty',
        'operation_type_cannot_be_empty' => 'Operation type cannot be empty',
        'entity_config_is_empty' => 'Entity (:class) configuration is empty',
    ],
];