<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get all the users
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'error_code' => null,
                'message' => 'User list fetched successfully!',
                'data' => $users,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user data
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserProfile($id)
    {
        try {
            $user = User::where('id', '=', $id);
            if ($user->exists()) {
                $user = $user->first();
            } else {
                $user = null;
            }

            return response()->json([
                'success' => true,
                'error_code' => null,
                'message' => 'User profile fetched successfully!',
                'data' => $user,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->input('id');
            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $role = $request->input('role');

            if ($auth_user->role === "admin" || (int)$auth_user->id === $id) {
                $user_profile = User::where('id', '=', $id);

                if ($user_profile->exists()) {
                    $user_profile = $user_profile->first();
                    $user_profile->first_name = $first_name;
                    $user_profile->last_name = $last_name;

                    if ($role && $auth_user->role === "admin") {
                        $user_profile->role = $role;
                    }

                    if ($request->hasfile('profile_photo')) {
                        $baseFolderName = '/users/';

                        $files = $request->file('profile_photo');
                        foreach ($files as $file) {
                            $_file = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                            $_file = $baseFolderName . $_file;
                            Storage::disk('local')->put('/public/uploads/' . $_file, file_get_contents($file));

                            $user_profile->profile_photo = $_file;
                        }
                    }

                    $user_profile->save();
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'User profile updated successfully!',
                    ], 200);
                }
            }

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change this action!',
            ], 403);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
