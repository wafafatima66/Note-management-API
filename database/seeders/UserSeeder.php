<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Mohammed',
            'last_name' => 'Nayeem',
            'email' => 'nayeem@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'first_name' => 'Md Karim',
            'last_name' => 'Ahmed',
            'email' => 'karim@gmail.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
