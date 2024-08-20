<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicineDosage;

class MedicineDosageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dosages = [
            ['remarks' => 'OD'],
            ['remarks' => 'BD'],
            ['remarks' => 'TD'],
            ['remarks' => 'QD'],
            ['remarks' => 'Hourly'],
        ];

        foreach ($dosages as $dosage) {
            MedicineDosage::create($dosage);
        }
    }
}
