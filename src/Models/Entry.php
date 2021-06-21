<?php

namespace Laragrad\MoneyEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laragrad\Uuid\Models\Concerns\HasUuidPrimaryKey;
use Laragrad\Models\Concerns\HasUserstamps;
use App\Laragrad\Eloquent\Model\Concerns\HasValidation;
use Illuminate\Database\Eloquent\Builder;

class Entry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUserstamps;
    
    const ENTRY_SYS_TYPE_NORMAL = 1;
    const ENTRY_SYS_TYPE_INVERSE = 2;
    const ENTRY_SYS_TYPE_STORNO = 3;
    
    const ENTRY_COMPENSATION_KIND_INVERSE = 1;
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
    
    protected $attributes = [
        'sys_type_code' => self::ENTRY_SYS_TYPE_NORMAL,
    ];
    
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        
        // Userstamps
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];
    
    public function operation()
    {
        return $this->belongsTo(Operation::class, 'operation_id', 'id');
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compensationEntry()
    {
        return $this->belongsTo(Entry::class, 'compensation_id');
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function compensatedEntry()
    {
        return $this->hasOne(Entry::class, 'compensation_id');
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function debitEntity()
    {
        return $this->morphTo('debitEntity', 'db_entity_type', 'db_entity_id');
    }

    /**
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
        return in_array($this->sys_type_code, [Entry::ENTRY_SYS_TYPE_INVERSE, Entry::ENTRY_SYS_TYPE_STORNO]);
    }
    
    /**
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeExecuted(Builder $query)
    {
        return $query->whereNull('compensation_id');
    }
}
