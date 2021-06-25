<?php

namespace Laragrad\MoneyEngine\Models\Concerns;

use Arr, Config;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;
use Laragrad\MoneyEngine\Models\Contracts\AccountableEntityInterface;
use Laragrad\MoneyEngine\Models\Entry;

trait Accountable
{
    /**
     * Stored entity configuration
     *
     * @var array
     */
    protected static $entityConfig = [];

    /**
     * Returns full or part of entity configuration
     *
     * @param string $path Configuration
     * @param mixed $default
     * @return mixed
     */
    public function entityConfig(string $path = null, $default = null)
    {
        if (!isset(static::$entityConfig[self::class])) {
            static::$entityConfig[self::class] = $this->loadEntityConfig();
        }

        $config = static::$entityConfig[self::class];

        if ($path) {
            return Arr::get($config, $path) ?? null;
        } else {
            return $config;
        }
    }

    /**
     * Load entity configuration
     *
     * @throws MoneyEngineException
     * @return array
     */
    protected function loadEntityConfig()
    {
        $config = Config::get("laragrad.laravel-money-engine.entities." . self::class);

        if (!is_array($config)) {
            throw new MoneyEngineException(trans(
                'laragrad/laravel-money-engine::messages.errors.entity_config_is_empty',
                ['class' => self::class]
            ));
        }

        $accounts = [];
        foreach ($config['accounts'] as $key) {
            $accounts[$key] = Config::get("laragrad.laravel-money-engine.account.{$key}");
        }
        $config['accounts'] = $accounts;

        // TODO Translations

        return $config;
    }

    /**
     * Get accountable sum column name
     *
     * @param string $accountType
     * @throws MoneyEngineException
     * @return string
     */
    public function getAccountSumColumn(string $accountType) : string
    {
        if (! is_array($config = $this->entityConfig("accounts.{$accountType}"))) {
            throw new MoneyEngineException(
                trans('laragrad/laravel-money-engine::messages.errors.account_type_is_incorrect', [
                    'type' => $accountType,
                    'entity' => self::class
                ])
            );
        }

        return $config['column'];
    }

    /**
     * Gets accountable sum
     *
     * @param string $accountType
     * @return float
     */
    public function getAccountSum(string $accountType) : float
    {
        return $this->{$this->getAccountSumColumn($accountType)};
    }

    /**
     * Sets accountable sum
     *
     * @param string $accountType
     * @param float $value
     */
    public function setAccountSum(string $accountType, float $value)
    {
        $this->validateAccountSum($accountType, $value);

        $this->{$this->getAccountSumColumn($accountType)} = $value;
    }

    /**
     * Changes accountable sum
     *
     * @param string $accountType
     * @param float $value
     * @param boolean $inverse
     * @return float
     */
    public function changeAccountSum(string $accountType, float $value) : float
    {
        $newSum = $this->getAccountSum($accountType) + $value;

        $this->setAccountSum($accountType, $newSum);

        return $newSum;
    }

    /**
     * Validate accountable sum
     *
     * @param string $accountType
     * @param float $value
     * @throws MoneyEngineException
     * @return boolean
     */
    public function validateAccountSum(string $accountType, float $value)
    {
        $accountKind = $this->entityConfig("accounts.{$accountType}.kind", null);

        if (!is_null($accountKind)) {
            if ($accountKind == AccountableEntityInterface::ACCOUNT_KIND_ACTIVE && $value > 0.00) {
                throw new MoneyEngineException(trans(
                    'laragrad/laravel-money-engine::messages.errors.active_account_rest_greater_zero'
                ));
            } else if ($accountKind == AccountableEntityInterface::ACCOUNT_KIND_PASSIVE && $value < 0.00) {
                throw new MoneyEngineException(trans(
                    'laragrad/laravel-money-engine::messages.errors.passive_account_rest_less_zero'
                ));
            }
        }

        return true;
    }

    /**
     * Debit entries relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function debitEntries()
    {
        return $this->morphMany(Entry::class, null, 'db_entity_type', 'db_entity_id');
    }

    /**
     * Credit entries relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function creditEntries()
    {
        return $this->morphMany(Entry::class, null, 'cr_entity_type', 'cr_entity_id');
    }

}