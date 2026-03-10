<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@auxfin.bf'],
            [
                'name'     => 'Administrateur',
                'email'    => 'admin@auxfin.bf',
                'password' => Hash::make('password'),
            ]
        );
    }
}
