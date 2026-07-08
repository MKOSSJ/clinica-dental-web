<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin Uno',
            'email'    => 'admin1@clinica.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Admin Dos',
            'email'    => 'admin2@clinica.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Staff Uno',
            'email'    => 'staff1@clinica.com',
            'password' => Hash::make('staff123'),
            'role'     => 'staff',
        ]);

        User::create([
            'name'     => 'Staff Dos',
            'email'    => 'staff2@clinica.com',
            'password' => Hash::make('staff123'),
            'role'     => 'staff',
        ]);
    }
}