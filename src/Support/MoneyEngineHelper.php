<?php 

namespace Laragrad\MoneyEngine\Support;

use DB;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;

class MoneyEngineHelper
{
    public static function checkTransactionStarted()
    {
        if (! \DB::connection()->transactionLevel()) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.transaction_must_be_started'));
        }
    }
}