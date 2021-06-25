<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laragrad\Support\Userstamps;

class CreateTmpBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_bills', function (Blueprint $table) {
            
            $table->uuid('id')->primary();
            $table->uuid('contract_id')->nullable();
            $table->date('bill_date');
            $table->string('bill_number', 25);
            $table->decimal('bill_sum', 15, 2);
            $table->decimal('paid_sum', 15, 2)->default(0.00);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_bills');
    }
}
