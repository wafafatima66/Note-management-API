<?php

namespace App\Http\Controllers;

use App\Models\NoteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteCategoryController extends Controller
{
    
    public function index()
    {
        try {
            $notesCats = NoteCategory::all();

            return response()->json([
                'success' => true,
                'error_code' => null,
                'message' => 'Note Categories fetched successfully!',
                'data' => $notesCats,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = (int)$request->input('id');
            $user_id = (int)$request->input('user_id');
            $parent_id = (int)$request->input('parent_id');
            $title = (string)$request->input('title');

            if (!$user_id > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user id!',
                ]);
            }


            if ($id > 0) {
                $noteCats = NoteCategory::where('id', '=', $id);
                if ($noteCats->exists()) {
                    $noteCats = $noteCats->first();
                } else {
                    $noteCats = new NoteCategory();
                }
            } else {
                $noteCats = new NoteCategory();
            }

            if ($user_id > 0) {
                $noteCats->user_id = $user_id;
            }

            if ($parent_id > 0) {
                $noteCats->parent_id = $parent_id;
            }

            if ($title !== "") {
                $noteCats->title = $title;
            }

            $noteCats->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Note Category saved successfully!',
                'data' => $noteCats,
            ]);
            
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

   
    public function show($id)
    {
        try {
            $noteCat = NoteCategory::where('id',"=",$id)->first();

            if ($noteCat !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Note Category data fetched successfully!',
                    'data' => $noteCat,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No note category found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    
    public function edit(NoteCategory $noteCategory)
    {
        //
    }

    
    public function update(UpdateNoteCategoryRequest $request, NoteCategory $noteCategory)
    {
        //
    }

    public function destroy($id)
    { 
        try {
            DB::beginTransaction();
            $notecat = NoteCategory::where('id', '=', $id);

            if ($notecat->exists()) {
                $notecat->delete();
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Note Category deleted successfully!'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
