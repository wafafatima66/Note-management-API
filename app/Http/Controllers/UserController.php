<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'error_code' => null,
            'message' => 'User list fetched successfully!',
            'data' => $users,
        ]);
    }
}
