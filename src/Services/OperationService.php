<?php 

namespace Laragrad\MoneyEngine\Services;

use Laragrad\MoneyEngine\Support\MoneyEngineHelper;
use Laragrad\MoneyEngine\Models\Operation;

class OperationService
{
    /**
     * 
     * @param Operation $operation
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    public function executeOperation(Operation $operation)
    {
        MoneyEngineHelper::checkTransactionStarted();
        
        return $operation->handler()->execute();
        
    }
    
    /**
     *
     * @param Operation $operation
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    public function deleteOperation(Operation $operation)
    {
        MoneyEngineHelper::checkTransactionStarted();
        
        return $operation->handler()->delete();
        
    }
}