<?php

namespace App\Http\Controllers\Docua;

use App\Http\Controllers\Controller;
use App\Models\DocuaNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function getNotes()
    {
        try {
            $notes = DocuaNote::all();
            return response()->json([
                'success' => true,
                'error_code' => null,
                'message' => 'Notes fetched successfully!',
                'data' => $notes,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function saveNotes(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = (int)$request->input('id');
            $user_id = (int)$request->input('user_id');
            $category_id = (int)$request->input('category_id');
            $title = (string)$request->input('title');
            $description = (string)$request->input('description');

            if (!$user_id > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user id!',
                ]);
            }

            if ($id > 0) {
                $note = DocuaNote::where('id', '=', $id);
                if ($note->exists()) {
                    $note = $note->first();
                } else {
                    $note = new DocuaNote();
                }
            } else {
                $note = new DocuaNote();
            }

            if ($user_id > 0) {
                $note->user_id = $user_id;
            }

            if ($category_id > 0) {
                $note->category_id = $category_id;
            }

            if ($title !== "") {
                $note->title = $title;
            }

            if ($description !== "") {
                $note->description = $description;
            }

            $note->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Note saved successfully!',
                'data' => $note,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }


    public function getNoteData($id)
    {
        try {
            $note = DocuaNote::where('id', "=", $id)->first();

            if ($note !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Note data fetched successfully!',
                    'data' => $note,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No note found!',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function deleteNote($id)
    {
        try {
            DB::beginTransaction();
            $note = DocuaNote::where('id', '=', $id);

            if ($note->exists()) {
                $note->delete();
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully!',
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
