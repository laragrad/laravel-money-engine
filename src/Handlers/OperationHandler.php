<?php 

namespace Laragrad\MoneyEngine\Handlers;

use App;
use Laragrad\MoneyEngine\Models\Operation;
use Illuminate\Validation\ValidationException;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;
use Laragrad\MoneyEngine\Services\EntryService;

class OperationHandler
{

    protected $operation;
    
    protected $entries;
    
    protected $entryService;
    
    /**
     * 
     * @param Operation $operation
     * @param EntryService $entryService
     */
    public function __construct(Operation $operation, EntryService $entryService)
    {
        $this->operation = $operation;
        $this->entryService = $entryService;
    }
    
    /**
     * Handler factory method
     * 
     * @param Operation $operation
     * @throws MoneyEngineException
     */
    public static function make(Operation $operation)
    {
        $handlerClass = $operation->operationConfig('handler');

        if (is_null($handlerClass)) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.operation_type_handler_is_undefined', [
                'type' => $operation->type_code,
            ]));
        } elseif (!class_exists($handlerClass)) {
            throw new MoneyEngineException(trans('laragrad/laravel-money-engine::messages.errors.operation_type_handler_class_is_not_exists', [
                'type' => $operation->type_code,
                'class' => $handlerClass,
            ]));
        }
        
        return \App::make($handlerClass, ['operation' => $operation]);
    }

    /**
     *
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    public function execute()
    {
        return $this->validateBeforeExecution()->makeEntries()->performExecution();
    }
    
    /**
     *
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    public function delete()
    {
        return $this->validateBeforeDeletion()->performDeletion();
    }
    
    /**
     * 
     * @return \Laragrad\MoneyEngine\Handlers\OperationHandler
     */
    protected function validateBeforeExecution() 
    {
        $this->validateOperationTypeRules();
        $this->validateCustomBeforeExecute();
        
        return $this;
    }

    protected function validateBeforeDeletion()
    {
        $this->validateCustomBeforeDelete();
        
        return $this;
    }
    
    /**
     * @throws ValidationException
     */
    protected function validateOperationTypeRules()
    {
        validator($this->operation->toArray(), $this->operation->operationConfig('validation.rules', []))
            ->validate();
    }
    
    /**
     *
     */
    protected function validateCustomBeforeExecute()
    {
        // Override it
    }
    
        /**
     *
     */
    protected function validateCustomBeforeDelete()
    {
        // Override it         
    }

/**
     * 
     * @return \Laragrad\MoneyEngine\Handlers\OperationHandler
     */
    protected function makeEntries()
    {
        return $this;
    }
    
    /**
     *
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    protected function performExecution()
    {
        $this->fireOperationEvent('executing');
        
        $this->operation->validate()->save();
        
        foreach ($this->operation->entries as $entry) {
            $this->entryService->executeEntry($this->operation, $entry);
        }
        
        $this->fireOperationEvent('executed');
        
        return $this->operation;
    }
    
    /**
     * 
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    protected function performDeletion()
    {
        $this->fireOperationEvent('deleting');
        
        foreach ($this->operation->entries as $entry) {
            $this->entryService->deleteEntry($entry);
        }

        $this->operation->delete();
        
        $this->fireOperationEvent('deleted');
        
        return $this->operation;
    }

    /**
     * 
     * @param string $event
     * @return boolean
     */
    protected function fireOperationEvent(string $event)
    {
        $method = \Str::ucfirst(\Str::camel($event));
        
        if (method_exists($this, $method)) {
            $this->$method($this->operation);
        }
        
        return true;
    }
}