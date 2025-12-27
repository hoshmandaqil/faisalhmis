<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        
        // Check if settings already exist
        $existingSettings = DB::table('settings')->first();
        
        if (!$existingSettings) {
            DB::table('settings')->insert([
                'half_data_mode' => 0, // Default to false/off
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
