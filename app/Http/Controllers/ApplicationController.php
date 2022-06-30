<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use \Exception;

class ApplicationController extends Controller
{
    /**
     * Get all the users
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllApplications(Request $request)
    {
        try {
            $Applications = Application::get();
            return response()->json([
                'success' => true,
                'error_code' => null,
                'message' => 'Application list fetched successfully!',
                'data' => $Applications,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
