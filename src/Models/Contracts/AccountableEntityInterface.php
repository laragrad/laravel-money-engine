<?php 

namespace Laragrad\MoneyEngine\Models\Contracts;

interface AccountableEntityInterface
{
    const ACCOUNT_KIND_NONE     = '*';
    const ACCOUNT_KIND_ACTIVE   = 'A';
    const ACCOUNT_KIND_PASSIVE  = 'P';
    
    /**
     * 
     * @param string $path
     * @param mixed $default
     */
    public function entityConfig(string $path = null, $default = null);
    
    /**
     * 
     * @param string $accountType
     * @return string
     */
    public function getAccountSumColumn(string $accountType) : string;
    
    /**
     * 
     * @param string $accountType
     * @return float
     */
    public function getAccountSum(string $accountType) : float;
    
    /**
     * 
     * @param string $accountType
     * @param float $value
     */
    public function setAccountSum(string $accountType, float $value);
    
    /**
     * 
     * @param string $accountType
     * @param float $value
     * @return float
     */
    public function changeAccountSum(string $accountType, float $value) : float;
    
    /**
     * 
     */
    public function debitEntries();
    
    /**
     * 
     */
    public function creditEntries();
}