<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
            ]
        );

        $role = Role::where(['name' => 'super_admin'])->first();

        $user->assignRole($role);

        $user2 = User::firstOrCreate(
            ['email' => 'wahyu@example.com'],
            [
                'name' => 'Wahyu',
                'password' => Hash::make('admin123'),
            ]
        );

        $role2 = Role::where(['name' => 'Manager'])->first();

        $user2->assignRole($role2);

        $user3 = User::firstOrCreate(
            ['email' => 'nursalam@example.com'],
            [
                'name' => 'Nur Salam',
                'password' => Hash::make('admin123'),
            ]
        );

        $role3 = Role::where(['name' => 'Karyawan'])->first();

        $user3->assignRole($role3);
    }
}
