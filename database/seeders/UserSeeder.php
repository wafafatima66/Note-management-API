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
        // id: 1
        User::create([
            'first_name' => 'Mohammed',
            'last_name' => 'Nayeem',
            'display_name' => 'Mohammed Nayeem',
            'email' => 'nayeem@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);

        // id: 2
        User::create([
            'first_name' => 'Acep',
            'last_name' => 'Hasanudin',
            'display_name' => 'Acep Hasanudin',
            'email' => 'acep@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        // id: 3
        User::create([
            'first_name' => 'Yusef',
            'last_name' => '',
            'display_name' => 'Yusef',
            'email' => 'yusef@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);

        // id: 4
        User::create([
            'first_name' => 'Teppei',
            'last_name' => '',
            'display_name' => 'Teppei',
            'email' => 'teppei@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);

        // id: 5
        User::create([
            'first_name' => 'Miyahara',
            'last_name' => '',
            'display_name' => 'Miyahara',
            'email' => 'miyahara@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => '',
        ]);
    }
}
