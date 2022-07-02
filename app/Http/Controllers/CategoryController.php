<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Category;

class CategoryController extends Controller
{
    public function getCategory(){

        try{

            $categories = Category::all();

            return response()->json([
                'success' => true,
                'message' => 'Categories fetched successfully!',
                'data' => $categories,
            ]);


        }catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }


        
    }
}
