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
            'profile_photo' => 'https://www.npmjs.com/npm-avatar/eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdmF0YXJVUkwiOiJodHRwczovL3MuZ3JhdmF0YXIuY29tL2F2YXRhci8xYTJkYmJiYmJmYjBjNTMyNTU5OGVlYjMwYmMzZTYyZT9zaXplPTEwMCZkZWZhdWx0PXJldHJvIn0.3-ENn3N4VD1eQb3SoxvUEpHdaXxKEBhheKZajg8KokU',
        ]);

        User::create([
            'first_name' => 'Md Karim',
            'last_name' => 'Ahmed',
            'display_name' => 'Md Karim Ahmed',
            'email' => 'karim@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'first_name' => 'Rahim',
            'last_name' => 'Ahmed',
            'display_name' => 'Rahim Ahmed',
            'email' => 'rahim@gmail.com',
            'password' => Hash::make('123456'),
            'profile_photo' => 'https://play-lh.googleusercontent.com/I-Yd5tJnxw7Ks8FUhUiFr8I4kohd9phv5sRFHG_-nSX9AAD6Rcy570NBZVFJBKpepmc=w240-h480-rw',
        ]);
    }
}
