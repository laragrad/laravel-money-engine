<?php 

namespace Laragrad\MoneyEngine\Models\Contracts;

interface AccountableEntityInterface
{
    const ACCOUNT_KIND_NONE     = '*';
    const ACCOUNT_KIND_ACTIVE   = 'A';
    const ACCOUNT_KIND_PASSIVE  = 'P';
    
    public function accountConfig(string $path = null, $default = null);
    
    public function getAccountSumColumn(string $accountType) : string;
    
    public function getAccountSum(string $accountType) : float;
    
    public function setAccountSum(string $accountType, float $value);
    
    public function changeAccountSum(string $accountType, float $value) : float;
    
    public function debitEntries();
    
    public function creditEntries();
}