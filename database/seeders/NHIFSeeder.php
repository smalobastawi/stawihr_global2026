<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NHIF;
class NHIFSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [

            ['range_start'=> 0.00,'range_end'=> 5999.00, 'amount_deductable'=>150.00, 'percentage'=>0, ],
            ['range_start'=> 6000.00,'range_end'=> 7999.00, 'amount_deductable'=>300.00, 'percentage'=>0, ],
            ['range_start'=> 8000.00,'range_end'=> 11999.00, 'amount_deductable'=>400.00, 'percentage'=>0, ],
            ['range_start'=> 12000.00, 'range_end'=>14999.00, 'amount_deductable'=>500.00,'percentage'=> 0, ],
            ['range_start'=> 15000.00, 'range_end'=>19999.00, 'amount_deductable'=>600.00,'percentage'=> 0, ],
            ['range_start'=> 20000.00,'range_end'=> 24999.00, 'amount_deductable'=>750.00,'percentage'=> 0, ],
            ['range_start'=> 25000.00,'range_end'=> 29999.00, 'amount_deductable'=>850.00,'percentage'=> 0, ],
            ['range_start'=> 30000.00, 'range_end'=>34999.00, 'amount_deductable'=>900.00,'percentage'=> 0, ],
            ['range_start'=> 35000.00,'range_end'=> 39999.00, 'amount_deductable'=>950.00, 'percentage'=>0, ],
            ['range_start'=> 40000.00, 'range_end'=>44999.00, 'amount_deductable'=>1000.00,'percentage'=> 0, ],
            ['range_start'=> 45000.00, 'range_end'=>4999.00, 'amount_deductable'=>1100.00, 'percentage'=>0, ],
            ['range_start'=> 50000.00,'range_end'=> 59999.00,'amount_deductable'=> 1200.00,'percentage'=> 0, ],
            ['range_start'=> 60000.00,'range_end'=> 69999.00,'amount_deductable'=> 1300.00,'percentage'=> 0, ],
            ['range_start'=> 70000.00,'range_end'=> 79999.00, 'amount_deductable'=>1400.00, 'percentage'=>0, ],
            ['range_start'=> 80000.00,'range_end'=> 89999.00, 'amount_deductable'=>1500.00, 'percentage'=>0, ],
            ['range_start'=> 90000.00,'range_end'=> 99999.00, 'amount_deductable'=>1600.00,'percentage'=> 0, ],
            ['range_start'=> 100000.00,'range_end'=> 999999.99, 'amount_deductable'=>1700.00, 'percentage'=>0,]

        ];

        foreach ($data as $amount_deductable) {
            NHIF::create($amount_deductable);
        }
    }
}
