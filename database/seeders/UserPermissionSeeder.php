<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Set the user ID and date
        $userId = 1;
        $now = Carbon::now();

        // Fetch all permission IDs you want to assign (assuming they are 1 to 25)
        $permissionIds = Permission::all()->pluck('id');

        // Prepare the data to insert
        $userPermissions = $permissionIds->map(function ($permissionId) use ($userId, $now) {
            return [
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        // Insert data into the user_permissions table
        UserPermission::insert($userPermissions);
    }
}
