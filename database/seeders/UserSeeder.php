<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'=>'Admin',
            'email'=>'admin@gmail.com',
            'is_admin'=>1,
            'password'=>Hash::make('admin'),
        ]);

        User::create([
            'name'=>'user',
            'email'=>'user@gmail.com',
            'is_admin'=>0,
            'password'=>Hash::make('user'),
        ]);
    }
}
