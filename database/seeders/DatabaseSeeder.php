<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $faker = Factory::create();
        $now = now();
        //        $faker->sentence
        //        $this->userSeed();
        //        $this->patientSeed($faker, $now);
        //        $this->medicineNameSeed($now, $faker);
        //        $this->pharmacySeed($now, $faker);
        //        $this->pharmacySeed($now, $faker);
        //        $this->patientMedicineSeed($now, $faker);
        $this->createPermissions($now);

        User::create([
            'id' => 1,
            'name' => 'aqil',
            'email' => 'aqil@email.com',
            'password' => Hash::make('sajjad321'),
            'type' => 1,
            'phone' => '0791635343'
        ]);
    }

    function userSeed()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Admin";
        $user->email = "admin@gmail.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 1;
        $user->phone = "0791635343";
        $user->save();

        $user = new User();
        $user->id = "2";
        $user->name = "Doctor";
        $user->email = "doctor@doctor.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 3;
        $user->phone = "0791635343";
        $user->save();

        $user = new User();
        $user->id = "3";
        $user->name = "Kh user";
        $user->email = "kh@doctor.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 3;
        $user->phone = "0791635343";
        $user->save();

        $user = new User();
        $user->id = "4";
        $user->name = "Pharmacy";
        $user->email = "pharmacy@pharmacy.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 2;
        $user->phone = "0791635343";
        $user->save();

        $user = new User();
        $user->id = "5";
        $user->name = "Reception";
        $user->email = "reception@reception.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 4;
        $user->phone = "0791635343";
        $user->save();

        $user = new User();
        $user->id = "6";
        $user->name = "KH Reception";
        $user->email = "khreception@reception.com";
        $user->password = Hash::make('sajjad321');
        $user->type = 4;
        $user->phone = "0791635343";
        $user->save();
    }

    // function patientSeed($faker, $now){

    //     for ($i=1 ; $i < 100000; $i++){
    //         $start = strtotime("2020-10-01");
    //         $end =  strtotime("2021-07-31");

    //         $randomDate = date("Y-m-d", rand($start, $end));
    //         \DB::table('patients')->insert([
    //             array('id' => $i,
    //                 'patient_name' => $faker->userName,
    //                 'patient_fname' => $faker->name,
    //                 'patient_phone' => $faker->phoneNumber,
    //                 'patient_generated_id' => 'RKHP-'.$i,
    //                 'gender' => 1,
    //                 'marital_status' => 1,
    //                 'age' => 30,
    //                 'blood_group' => 'A',
    //                 'advance_pay' => '0',
    //                 'doctor_id' => rand(2, 3),
    //                 'reg_date' => $randomDate,
    //                 'created_by' => rand(5, 6),
    //                 'created_at' => $now,'updated_at' => $now),
    //         ]);
    //     }
    // }

    // function medicineNameSeed($now, $faker){
    //     for ($i=1 ; $i < 1000; $i++){
    //         $start = strtotime("2020-10-01");
    //         $end =  strtotime("2021-07-31");

    //         $randomDate = date("Y-m-d", rand($start, $end));
    //         \DB::table('medicine_names')->insert([
    //             array('id' => $i,
    //                 'medicine_name' => $faker->city,
    //                 'created_at' => $now,'updated_at' => $now),
    //         ]);
    //     }

    // }

    // function pharmacySeed($now, $faker){
    //     for ($i=1 ; $i < 5000; $i++){
    //         $start = strtotime("2020-10-01");
    //         $end =  strtotime("2021-07-31");

    //         $randomDate = date("Y-m-d", rand($start, $end));
    //         \DB::table('pharmacies')->insert([
    //             array('id' => $i,
    //                 'medicine_id' => rand(1, 600),
    //                 'supplier_id' => rand(1, 2),
    //                 'quantity' => rand(80,500),
    //                 'purchase_qty' => rand(80,500),
    //                 'purchase_price' => rand(10,100),
    //                 'sale_percentage' => rand(10,40),
    //                 'sale_price' => rand(200,500),
    //                 'vendor' => $faker->company,
    //                 'invoice_no' => 'RKH_'.rand(1, 50),
    //                 'mfg_date' => $randomDate,
    //                 'exp_date' => $randomDate,
    //                 'created_by' => 4,
    //                 'created_at' => $now,'updated_at' => $now),
    //         ]);
    //     }

    // }

    // function patientMedicineSeed($now, $faker){
    //     for ($i=1 ; $i < 10000; $i++){
    //         $start = strtotime("2020-10-01");
    //         $end =  strtotime("2021-07-31");

    //         $randomDate = date("Y-m-d", rand($start, $end));
    //         \DB::table('patient_medicines')->insert([
    //             array('id' => $i,
    //                 'patient_id' => rand(90000, 99999),
    //                 'medicine_id' => rand(1, 1000),
    //                 'quantity' => rand(20,80),
    //                 'status' => 0,
    //                 'remark' => $faker->sentence,
    //                 'created_by' => rand(2,3),
    //                 'created_at' => $now,'updated_at' => $now),
    //         ]);
    //     }

    // }

    function createPermissions($now)
    {
        $permissionArray = [
            'Suppliers' => ['supplier_list', 'add_supplier', 'edit_supplier', 'delete_supplier'],
            'Floors' => ['floor_list', 'add_floor', 'edit_floor', 'delete_floor'],
            'Laboratory' => ['lab_list', 'add_lab', 'edit_lab', 'delete_lab'],
            'Patients' => ['patient_list', 'add_patient', 'edit_patient', 'delete_patient'],
            'Doctor' => ['doctor_sale_medicine', 'doctor_sale_ipd', 'doctor_set_lab', 'doctor_request_medicine', 'doctor_edit_sale_medicine'],
            'Pharmacy' => ['pharmacy_menu', 'pharmacy_sale_medicine', 'pharmacy_preview_medicine', 'pharmacy_edit_medicine', 'pharmacy_complete_medicine'],
            'Reception' => ['reception_menu', 'reception_preview_medicine', 'reception_print_medicine'],
            'Reports' => [
                'reports_view', 'datewise_procurement_report', 'datewise_sale_report', 'available_stock_report', 'pharmacy_percentage_report',
                'short_pharmacy_report', 'expired_medicine_report', 'request_medicine_report', 'medication_report'
            ],
            'Other Setting' => [
                'setting_view', 'user_list',
                'user_add', 'user_edit', 'user_delete', 'user_deactivate'
            ]
        ];

        foreach ($permissionArray as $key => $permission) {
            foreach ($permission as $perm) {
                \DB::table('permissions')->insert([
                    array(
                        'permission_name' => $perm,
                        'permission_group' => $key,
                        'created_at' => $now, 'updated_at' => $now
                    ),
                ]);
            }
        }
    }
}
