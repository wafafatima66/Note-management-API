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
            'display_name' => 'Mohammed Nayeem',
            'email' => 'nayeem@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);

        User::create([
            'first_name' => 'Acep',
            'last_name' => 'Hasanudin',
            'display_name' => 'Acep Hasanudin',
            'email' => 'acep@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'first_name' => 'Yusuf',
            'last_name' => '',
            'display_name' => 'Yusuf',
            'email' => 'yusuf@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);
    }
}
