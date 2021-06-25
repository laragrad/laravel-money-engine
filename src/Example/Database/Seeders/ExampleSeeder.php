<?php

namespace Laragrad\MoneyEngine\Example\Database\Seeders;

use Illuminate\Database\Seeder;
use Laragrad\MoneyEngine\Example\Models\TmpBankPayment;
use Laragrad\MoneyEngine\Example\Models\TmpBill;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p = new TmpBankPayment();
        $p->doc_date = now();
        $p->doc_number = '1';
        $p->rest_sum = $p->doc_sum = 500.00;
        $p->purpose = 'Pay for bill #1 200.00 and #2 300.00';
        $p->save();
        $this->command->info("Bank payment #1 created : {$p->id}");
        
        $b1 = new TmpBill();
        $b1->bill_date = now()->subDays(2);
        $b1->bill_number = '1';
        $b1->bill_sum = 200.00;
        $b1->save();
        $this->command->info("Bill #1 created : {$b1->id}");
        
        $b2 = new TmpBill();
        $b2->bill_date = now()->subDays(1);
        $b2->bill_number = '2';
        $b2->bill_sum = 300.00;
        $b2->save();
        $this->command->info("Bill #2 created : {$b2->id}");
        
    }
}
