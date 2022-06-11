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
    }
}
