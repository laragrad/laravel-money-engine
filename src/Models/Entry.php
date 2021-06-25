<?php

namespace Laragrad\MoneyEngine\Models;

use Arr, Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Laragrad\Models\Concerns\HasUserstamps;
use Laragrad\MoneyEngine\Exceptions\MoneyEngineException;

class Entry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUserstamps;

    const ENTRY_SYS_TYPE_NORMAL = 1;
    const ENTRY_SYS_TYPE_REVERSE = 2;
    const ENTRY_SYS_TYPE_STORNO = 3;

    const ENTRY_COMPENSATION_KIND_REVERSE = 1;
    const ENTRY_COMPENSATION_KIND_STORNO = 2;

    const ENTRY_TYPE_BANK_INCOMING = 1;
    const ENTRY_TYPE_PAYMENT = 2;

    protected $table = 'money_engine_entries';

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

    /**
     * Initialisation attribute values
     *
     * @var array
     */
    protected $attributes = [
        'sys_type_code' => self::ENTRY_SYS_TYPE_NORMAL,
    ];

    /**
     * Casts attributes
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',              // Entry ID

        'sys_type_code' => 'integer',   // Entry system type
        'type_code' => 'integer',       // Entry type code
        'entry_date' => 'date',         // Entry date
        'accounting_date' => 'date',    // Accounting date
        'operation_id' => 'integer',    // Operation ID

        // Debit entry part
        'db_account_code' => 'string',  // Debit account type code
        'db_entity_type' => 'string',   // Debit entity type
        'db_entity_id' => 'string',     // Debit entity ID
        'db_sum' => 'real',             // Debit sum
        'db_currency_code' => 'string', // Debit currency ISO code

        // Credit entry part
        'cr_account_code' => 'string',  // Credit account type code
        'cr_entity_type' => 'string',   // Credit entity type
        'cr_entity_id' => 'string',     // Credit entity ID
        'cr_sum' => 'real',             // Credit sum
        'cr_currency_code' => 'string', // Credit currency ISO code

        'compensation_id' => 'integer', // Compensation entry ID
        'compensation_kind_code' => 'integer',  // Compensation kind
        'details' => 'json',            // Other entry details

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
     * Entry configuration
     *
     * @var array
     */
    protected static $config;

    /**
     * Related operation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'id');
    }

    /**
     * Related compensation entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compensationEntry()
    {
        return $this->belongsTo(Entry::class, 'compensation_id');
    }

    /**
     * Related compensated entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function compensatedEntry()
    {
        return $this->hasOne(Entry::class, 'compensation_id');
    }

    /**
     * Related debit entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function debitEntity()
    {
        return $this->morphTo('debitEntity', 'db_entity_type', 'db_entity_id');
    }

    /**
     * Related credit entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function creditEntity()
    {
        return $this->morphTo('debitEntity', 'cr_entity_type', 'cr_entity_id');
    }

    /**
     *
     * @return boolean
     */
    public function isCompensation()
    {
        return $this->config("sys_types.{$this->sys_type_code}.is_compensation");
    }

    /**
     * Scope to select not conmpensated entries
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExecuted(Builder $query)
    {
        return $query->whereNull('compensation_id');
    }

    /**
     * Returns full or partial entries configuration
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
     * Returns this entry configuration
     *
     * @param string $path
     * @param mixed $default
     * @throws MoneyEngineException
     * @return array|mixed
     */
    public function entryConfig(string $path = null, $default = null)
    {
        if (is_null($this->type_code)) {
            throw new MoneyEngineException(trans(
                'laragrad/laravel-money-engine::messages.errors.entry_type_cannot_be_empty'
            ));
        }

        $path = is_string($path) ? "types.{$this->type_code}.{$path}" : "types.{$this->type_code}";

        return $this->config($path, $default);
    }

    /**
     * Load entries configuraton
     *
     * @throws MoneyEngineException
     * @return array
     */
    protected function loadConfig()
    {
        $config = Config::get('laragrad.laravel-money-engine.entry');

        if (!is_array($config)) {
            throw new MoneyEngineException(trans(
                'laragrad/laravel-money-engine::messages.errors.entry_config_is_empty'
            ));
        }

        // TODO translations

        return $config;
    }

    /**
     *
     * @return \Laragrad\MoneyEngine\Models\Entry
     */
    public function validate()
    {
        // TODO validation

        return $this;
    }

    /**
     *
     *
     * @param string|null $value
     */
    public function setDbAccountCodeAttribute($value)
    {
        $this->attributes['db_account_code'] = $value;
        $entityClass = config("laragrad.laravel-money-engine.account.{$value}.entity");
        if (!isset($this->attributes['db_entity_type'])) {
            $this->attributes['db_entity_type'] = $entityClass;
        } elseif ($entityClass <> $this->attributes['db_entity_type']) {
            // TODO Exception
        }
    }

    /**
     *
     * @param string|null $value
     */
    public function setCrAccountCodeAttribute($value)
    {
        $this->attributes['cr_account_code'] = $value;
        $entityClass = config("laragrad.laravel-money-engine.account.{$value}.entity");
        if (!isset($this->attributes['cr_entity_type'])) {
            $this->attributes['cr_entity_type'] = $entityClass;
        } elseif ($entityClass <> $this->attributes['cr_entity_type']) {
            // TODO Exception
        }
    }
}
