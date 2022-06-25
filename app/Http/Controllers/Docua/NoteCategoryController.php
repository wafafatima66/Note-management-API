<?php

namespace App\Http\Controllers\Docua;

use App\Http\Controllers\Controller;
use App\Models\DocuaNoteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteCategoryController extends Controller
{
    public function getNoteCategories()
    {
        try {
            $notesCats = DocuaNoteCategory::all();

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

    public function saveNoteCategories(Request $request)
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
                $noteCats = DocuaNoteCategory::where('id', '=', $id);
                if ($noteCats->exists()) {
                    $noteCats = $noteCats->first();
                } else {
                    $noteCats = new DocuaNoteCategory();
                }
            } else {
                $noteCats = new DocuaNoteCategory();
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


    public function getNoteCategoryData($id)
    {
        try {
            $noteCat = DocuaNoteCategory::where('id', "=", $id)->first();

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

    public function deleteNoteCategory($id)
    {
        try {
            DB::beginTransaction();
            $notecat = DocuaNoteCategory::where('id', '=', $id);

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
