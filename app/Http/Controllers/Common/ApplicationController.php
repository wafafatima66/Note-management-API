<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\UserInstalledApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function getUserApplications() {

        try {
            $auth_user = auth()->user();
            $applications = UserInstalledApplication::where('user_id', '=', $auth_user->id)
            ->orderBy('id', 'desc')
            ->with('application')
            ->get()
            ->pluck('application');

            return response()->json([
                'success' => true,
                'message' => 'Applications fetched successfully!',
                'data' => $applications,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
