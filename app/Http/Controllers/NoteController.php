<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    
    public function index()
    {
        try {
            $notes = Note::all();

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
                $note = Note::where('id', '=', $id);
                if ($note->exists()) {
                    $note = $note->first();
                } else {
                    $note = new Note();
                }
            } else {
                $note = new Note();
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

   
    public function show($id)
    {
        try {
            $note = Note::where('id',"=",$id)->first();

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

  
    public function edit(Note $note)
    {
        //
    }

   
    public function update(UpdateNoteRequest $request, Note $note)
    {
        //
    }

  
    public function destroy($id)
    { 
        try {
            DB::beginTransaction();
            $note = Note::where('id', '=', $id);

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
