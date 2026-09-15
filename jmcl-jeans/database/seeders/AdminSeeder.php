<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'superadmin@jmcljeans.test'],
            [
                'name' => 'JMCL Super Admin',
                'password' => Hash::make('password'), // CHANGE THIS after first login
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'admin@jmcljeans.test'],
            [
                'name' => 'JMCL Store Admin',
                'password' => Hash::make('password'), // CHANGE THIS after first login
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
