<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['id' => 1,
            'username' => 'Test User',
            'email' => 'test@email.com',
            'password' => Hash::make('password'),
            'level_id' => Level::first()->id,
            'level_points' => 40,
        ]);
    }
}
