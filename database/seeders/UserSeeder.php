<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => config('seeder.super_admin_email')],
            [
                'name' => config('seeder.super_admin_name'),
                'password' => Hash::make((string) config('seeder.super_admin_password')),
                'email_verified_at' => now(),
            ],
        );
        $superAdmin->assignRole('super-admin');

        $admin = User::firstOrCreate(
            ['email' => 'manager@laraplate.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $admin->assignRole('admin');

        $editor = User::firstOrCreate(
            ['email' => 'editor@laraplate.test'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
        $editor->assignRole('editor');

        User::firstOrCreate(
            ['email' => 'alice@laraplate.test'],
            [
                'name' => 'Alice Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'bob@laraplate.test'],
            [
                'name' => 'Bob Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
