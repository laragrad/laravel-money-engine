<?php

namespace Laragrad\MoneyEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laragrad\Uuid\Models\Concerns\HasUuidPrimaryKey;
use Laragrad\Models\Concerns\HasUserstamps;
use App\Laragrad\Eloquent\Model\Concerns\HasValidation;

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
        'operation_date' => 'date',         // Operation date
        'accounting_date' => 'date',    // Accounting date
        'details' => 'json',            // Other operation details
        
        // Timestamps
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        
        // Userstamps
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];
    
    
}
