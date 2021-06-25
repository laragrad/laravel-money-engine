<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laragrad\Support\Userstamps;

class CreateTmpBankPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_bank_payments', function (Blueprint $table) {
            
            $table->uuid('id')->primary();
            
            $table->date('doc_date');
            $table->string('doc_number', 25);
            $table->decimal('doc_sum', 15, 2);
            $table->decimal('rest_sum', 15, 2)->default(0.00);
            $table->string('purpose', 512);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_bank_payments');
    }
}
