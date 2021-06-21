<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laragrad\Support\Userstamps;

class CreateMoneyEngineOperationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('money_engine_operations', function (Blueprint $table) {
            
            $table->id('id');
            
            $table->integer('type_code');
            $table->date('operation_date');
            $table->date('accounting_date');
            
            $table->jsonb('details')->nullable();
            
            // timestamps
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            
            // userstamps
            Userstamps::addUserstampsColumns($table, true);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('money_engine_operations');
    }
}
