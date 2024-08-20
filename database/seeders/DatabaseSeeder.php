<?php

namespace Database\Seeders;

use App\Models\User;
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
        $now = now();

        $this->userSeed();

        $this->createPermissions($now);

        $this->call(UserPermissionSeeder::class);

        $this->call(MedicineDosageSeeder::class);

        User::create([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('password'),
            'type' => 1,
            'phone' => '0791635343'
        ]);
    }

    function userSeed()
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('sajjad321'),
                'type' => 1,
                'phone' => '0791635343',
            ],
            [
                'id' => 2,
                'name' => 'Doctor',
                'email' => 'doctor@doctor.com',
                'password' => Hash::make('sajjad321'),
                'type' => 3,
                'phone' => '0791635343',
            ],
            [
                'id' => 3,
                'name' => 'Kh user',
                'email' => 'kh@doctor.com',
                'password' => Hash::make('sajjad321'),
                'type' => 3,
                'phone' => '0791635343',
            ],
            [
                'id' => 4,
                'name' => 'Pharmacy',
                'email' => 'pharmacy@pharmacy.com',
                'password' => Hash::make('sajjad321'),
                'type' => 2,
                'phone' => '0791635343',
            ],
            [
                'id' => 5,
                'name' => 'Reception',
                'email' => 'reception@reception.com',
                'password' => Hash::make('sajjad321'),
                'type' => 4,
                'phone' => '0791635343',
            ],
            [
                'id' => 6,
                'name' => 'KH Reception',
                'email' => 'khreception@reception.com',
                'password' => Hash::make('sajjad321'),
                'type' => 4,
                'phone' => '0791635343',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }

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
                'reports_view',
                'datewise_procurement_report',
                'datewise_sale_report',
                'available_stock_report',
                'pharmacy_percentage_report',
                'short_pharmacy_report',
                'expired_medicine_report',
                'request_medicine_report',
                'medication_report'
            ],
            'Other Setting' => [
                'setting_view',
                'user_list',
                'user_add',
                'user_edit',
                'user_delete',
                'user_deactivate'
            ]
        ];

        foreach ($permissionArray as $group => $permissions) {
            foreach ($permissions as $permission) {
                DB::table('permissions')->insert([
                    'permission_name' => $permission,
                    'permission_group' => $group,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }
}
