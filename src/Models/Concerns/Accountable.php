<?php

namespace Laragrad\MoneyEngine\Models\Concerns;


trait Accountable
{
    static $entityConfig = [];

    /**
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function accountConfig(string $path = null, $default = null)
    {
        if (is_null(static::$entityConfig) || !isset(static::$entityConfig[self::class])) {
            $config = \Config::get("laragrad.laravel-money-engine.entity." . self::class);

            $accounts = [];
            foreach ($config['accounts'] as $key) {
                $accounts[$key] = \Config::get("laragrad.laravel-money-engine.account.{$key}");
            }
            $config['accounts'] = $accounts;
            static::$entityConfig[self::class] = $config;
        }

        if ($path) {
            return \Arr::get(static::$entityConfig[self::class], $path) ?? null;
        }

        return static::$entityConfig[self::class];
    }

    /**
     *
     * @param string $accountType
     * @throws \RuntimeException
     * @return string
     */
    public function getAccountSumColumn(string $accountType) : string
    {
        if (! is_array($config = $this->accountConfig("accounts.{$accountType}"))) {
            throw new \Exception(
                trans('laragrad/laravel-money-engine:messages.errors.account_type_is_incorrect', [
                    'type' => $accountType,
                    'entity' => self::class
                ])
            );
        }

        return $config['column'];
    }

    /**
     *
     * @param string $accountType
     * @return float
     */
    public function getAccountSum(string $accountType) : float
    {
        return $this->{$this->getAccountSumColumn($accountType)};
    }

    /**
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
     *
     * @param string $accountType
     * @param float $value
     * @throws \Exception
     * @return boolean
     */
    public function validateAccountSum(string $accountType, float $value)
    {
        $accountKind = $this->accountConfig("accounts.{$accountType}.kind", null);

        if (!is_null($accountKind)) {
            if ($accountKind == AccountableEntityInterface::ACCOUNT_KIND_ACTIVE && $value > 0.00) {
                throw new \Exception(trans('laragrad/laravel-money-engine:messages.errors.active_account_rest_greater_zero'));
            } else if ($accountKind == AccountableEntityInterface::ACCOUNT_KIND_PASSIVE && $value < 0.00) {
                throw new \Exception(trans('laragrad/laravel-money-engine:messages.errors.passive_account_rest_less_zero'));
            }
        }

        return true;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function debitEntries()
    {
        return $this->morphMany(Entry::class, null, 'db_entity_type', 'db_entity_id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function creditEntries()
    {
        return $this->morphMany(Entry::class, null, 'cr_entity_type', 'cr_entity_id');
    }

}