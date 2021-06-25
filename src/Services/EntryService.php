<?php

namespace Laragrad\MoneyEngine\Services;

use Laragrad\MoneyEngine\Support\MoneyEngineHelper;
use Laragrad\MoneyEngine\Models\Contracts\AccountableEntityInterface as Account;
use Laragrad\MoneyEngine\Models\Entry;
use Laragrad\MoneyEngine\Models\Operation;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;

class EntryService
{

    /**
     * Execute new entry
     *
     * @param Entry $entry
     */
    public function executeEntry(Operation $operation, Entry $entry)
    {
        MoneyEngineHelper::checkTransactionStarted();

        $entry->debitEntity->changeAccountSum($entry->db_account_code, $entry->db_sum);
        $entry->debitEntity->save();

        $entry->creditEntity->changeAccountSum($entry->cr_account_code, $entry->cr_sum);
        $entry->creditEntity->save();

        $entry->operation_id = $operation->id;
        $entry->validate()->save();

        return $entry;
    }

    /**
     * Execute storno entry for existing entry
     *
     * @param Entry $entry
     * @param Operation $operation
     * @throws MoneyEngineException
     */
    public function executeStornoEntry(Operation $operation, Entry $entry)
    {
        MoneyEngineHelper::checkTransactionStarted();

        if ($entry->compensation_kind_code) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.entry_has_been_already_compensated'));
        }

        if ($entry->sys_type_code != Entry::ENTRY_SYS_TYPE_NORMAL) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.compensation_cannot_be_compensated'));
        }

        // Preparing compensation entry
        $compensationEntry = new Entry();

        $compensationEntry->sys_type_code = Entry::ENTRY_SYS_TYPE_STORNO;
        $compensationEntry->type_code = $entry->type_code;
        $compensationEntry->operation_id = $operation->id;
        $compensationEntry->entry_date = now()->format('Y-m-d');
        $compensationEntry->accounting_date = now()->format('Y-m-d');

        $compensationEntry->db_account_code = $entry->db_account_code;
        $compensationEntry->db_entity_type = $entry->db_entity_type;
        $compensationEntry->db_entity_id = $entry->db_entity_id;
        $compensationEntry->db_sum = - $entry->db_sum;
        $compensationEntry->db_currency_code = $entry->db_currency_code;

        $compensationEntry->cr_account_code = $entry->cr_account_code;
        $compensationEntry->cr_entity_type = $entry->cr_entity_type;
        $compensationEntry->cr_entity_id = $entry->cr_entity_id;
        $compensationEntry->cr_sum = - $entry->cr_sum;
        $compensationEntry->cr_currency_code = $entry->cr_currency_code;

        $compensationEntry->validate()->save();

        // Debit account sum updating
        $compensationEntry->debitEntity->changeAccountSum($entry->db_account_code, $compensationEntry->db_sum);
        $compensationEntry->debitEntity->save();

        // Credit account sum updating
        $compensationEntry->creditEntity->changeAccountSum($entry->cr_account_code, $compensationEntry->cr_sum);
        $compensationEntry->creditEntity->save();

        // Compensated entry updating
        $entry->compensation_id = $compensationEntry->id;
        $entry->compensation_kind_code = Entry::ENTRY_COMPENSATION_KIND_STORNO;
        $entry->validate()->save();

        return $compensationEntry;
    }

    /**
     * Execure reverse entry for existing entry
     *
     * @param Operation $operation
     * @param Entry $entry
     * @throws MoneyEngineException
     */
    public function executeReverseEntry(Operation $operation, Entry $entry)
    {
        MoneyEngineHelper::checkTransactionStarted();

        if ($entry->compensation_kind_code) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.entry_has_been_already_compensated'));
        }

        if ($entry->sys_type_code != Entry::ENTRY_SYS_TYPE_NORMAL) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.compensation_cannot_be_compensated'));
        }

        // Preparing compensation entry
        $compensationEntry = new Entry();

        $compensationEntry->sys_type_code = Entry::ENTRY_SYS_TYPE_REVERSE;
        $compensationEntry->type_code = $entry->type_code;
        $compensationEntry->operation_id = $operation->id;
        $compensationEntry->entry_date = now()->format('Y-m-d');
        $compensationEntry->accounting_date = now()->format('Y-m-d');

        $compensationEntry->db_account_code = $entry->cr_account_code;
        $compensationEntry->db_entity_type = $entry->cr_entity_type;
        $compensationEntry->db_entity_id = $entry->cr_entity_id;
        $compensationEntry->db_sum = $entry->cr_sum;
        $compensationEntry->db_currency_code = $entry->cr_currency_code;

        $compensationEntry->cr_account_code = $entry->db_account_code;
        $compensationEntry->cr_entity_type = $entry->db_entity_type;
        $compensationEntry->cr_entity_id = $entry->db_entity_id;
        $compensationEntry->cr_sum = $entry->db_sum;
        $compensationEntry->cr_currency_code = $entry->db_currency_code;

        $compensationEntry->validate()->save();

        // Debit account sum updating
        $entry->debitEntity->changeAccountSum($entry->db_account_code, $compensationEntry->db_sum);
        $entry->debitEntity->save();

        // Credit account sum updating
        $entry->creditEntity->changeAccountSum($entry->cr_account_code, $compensationEntry->cr_sum);
        $entry->creditEntity->save();

        // Compensated entry updating
        $entry->compensation_id = $compensationEntry->id;
        $entry->compensation_kind_code = Entry::ENTRY_COMPENSATION_KIND_REVERSE;
        $entry->validate()->save();

        return $compensationEntry;
    }

    /**
     * Delete existing entry
     *
     * @param Entry $entry
     * @return \Laragrad\MoneyEngine\Models\Entry
     */
    public function deleteEntry(Entry $entry)
    {
        MoneyEngineHelper::checkTransactionStarted();

        $entry->debitEntity->changeAccountSum($entry->db_account_code, -$entry->db_sum);
        $entry->debitEntity->save();

        $entry->creditEntity->changeAccountSum($entry->cr_account_code, -$entry->cr_sum);
        $entry->creditEntity->save();

        $entry->delete();

        if ($entry->isCompensation()) {

            $entry->compensatedEntry->compensation_id = null;
            $entry->compensatedEntry->compensation_kind = null;
            $entry->compensatedEntry->validate()->save();
        }

        return $entry;
    }
}