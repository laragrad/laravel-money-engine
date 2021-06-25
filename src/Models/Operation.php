<?php

namespace Laragrad\MoneyEngine\Models;

use Arr, Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laragrad\Models\Concerns\HasUserstamps;
use Laragrad\MoneyEngine\Handlers\OperationHandler;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;

class Operation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUserstamps;

    const OPERATION_TYPE_BANK_INCOMING = 1,
          OPERATION_TYPE_PAYMENT = 2;

    protected $table = 'money_engine_operations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * HasUserstamps: Indicates if the model should be userstamped.
     *
     * @var boolean
     */
    public $userstamps = true;

    /**
     * HasValidation: Model calculated attributes
     *
     * @var array
     */
    protected $calculated = [
        'created_at', 'updated_at', 'deleted_at',
        'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'id' => 'integer',              // Operation ID

        'type_code' => 'integer',       // Operation type code
        'operation_date' => 'date',     // Operation date
        'accounting_date' => 'date',    // Accounting date
        'details' => 'json',            // Other operation details

        // Timestamps
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',

        // Userstamps
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    /**
     *
     * @var array
     */
    protected static $config;

    /**
     *
     * @var OperationHandler
     */
    protected $handler;

    /**
     * Get the operation hndler
     *
     * @return OperationHandler
     */
    public function handler()
    {
        if (is_null($this->handler)) {
            $this->handler = OperationHandler::make($this);
        }

        return $this->handler;
    }

    /**
     * Related entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    /**
     *
     *
     * @param string $path
     * @param mixed $default
     * @return array|mixed
     */
    public function config(string $path = null, $default = null)
    {
        if (is_null(static::$config)) {
            static::$config = $this->loadConfig();
        }

        if (is_string($path)) {
            return \Arr::get(static::$config, $path, $default);
        }

        return static::$config;
    }

    /**
     *
     * @param string $path
     * @param mixed $default
     * @throws MoneyEngineException
     * @return array|mixed
     */
    public function operationConfig(string $path = null, $default = null)
    {
        if (is_null($this->type_code)) {
            throw new MoneyEngineException(trans(
                'laragrad/laravel-money-engine::messages.errors.operation_type_cannot_be_empty'
            ));
        }

        $path = is_string($path) ? "types.{$this->type_code}.{$path}" : "types.{$this->type_code}";

        return $this->config($path, $default);
    }

    /**
     *
     * @throws MoneyEngineException
     * @return array
     */
    protected function loadConfig()
    {
        $config = Config::get('laragrad.laravel-money-engine.operation');

        if (!is_array($config)) {
            throw new MoneyEngineException(trans(
                'laragrad/laravel-money-engine::messages.errors.operation_config_is_empty'
            ));
        }

        // TODO translations

        return $config;
    }

    /**
     *
     * @return \Laragrad\MoneyEngine\Models\Operation
     */
    public function validate()
    {
        // TODO validation

        return $this;
    }
}
