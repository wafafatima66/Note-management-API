<?php

namespace App\Http\Controllers;

use App\Models\MessageNote;
use App\Models\MessageNoteComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageNotesController extends Controller
{
    /**
     * Get all the chat notes
     * @param $connection_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatNotes($connection_id, Request $request)
    {
        try {
            $auth_user = auth()->user();
            $search = (string)$request->input('search');
            $category_id = (int)$request->input('category_id');
            $notes = MessageNote::where('message_connection_id', '=', $connection_id)
                ->where('user_id', '=', $auth_user->id)
                ->when(trim($search) !== "", function ($q) use ($search) {
                    return $q->where('title', 'LIKE', '%' . $search . '%')->orWhere('description', 'LIKE', '%' . $search . '%');
                })
                ->when($category_id, function($q) use ($category_id) {
                    return $q->where('category_id', $category_id);
                })
                ->orderBy('id', 'desc')->get();

            foreach ($notes as $note) {
                $note->date = date('d-m-Y', strtotime($note->created_at));
                if(!$note->category_id){
                    $note->category_id = 0;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Message notes fetched successfully!',
                'data' => $notes,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    
    /**
     * Update chat note
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveChatNote(Request $request)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->input('id');
            $connection_id = (int)$request->input('room_id');
            $category_id = (int)$request->input('category_id') ;
            $title = $request->input('title');
            $description = $request->input('description');

            if ($id > 0) {
                $note = MessageNote::where('id', '=', $id)
                    ->where('user_id', '=', $auth_user->id);
                if ($note->exists()) {
                    $note = $note->first();
                } else {
                    $note = new MessageNote();
                }
            } else {
                $note = new MessageNote();
            }

            $note->message_connection_id = $connection_id;
            $note->user_id = $auth_user->id;
            $note->category_id = $category_id;
            $note->title = $title;
            $note->description = $description;
            $note->save();

            $note_data = $note;
            $note_data->date = date('d-m-Y', strtotime($note->created_at));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Note saved successfully!',
                'data' => $note_data,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete chat note
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChatNote($id)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();

            $note = MessageNote::where('id', '=', $id)
                ->where('user_id', '=', $auth_user->id);

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


    /**
     * Get all comments of a chat note
     * @param $connection_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatNoteWithComment($connection_id, $note_id )
    {
        try {
            $auth_user = auth()->user();
            
            $notes = MessageNote::where('message_connection_id', '=', $connection_id)
                ->where('id', $note_id)
                ->first();

            $comments = MessageNoteComment::where('message_connection_id', '=', $connection_id)
                        ->with('sender')
                        ->where('note_id', $notes->id)
                        ->get();
            foreach($comments AS $comment){
                $comment->date = date('d-m-Y', strtotime($comment->created_at));
                $comment->date_time = date('h:ia - d M, Y', strtotime($comment->created_at));
                $comment->display_time = date('h:i a', strtotime($comment->created_at));
            }
            
            $notes->date = date('d-m-Y', strtotime($notes->created_at));
            $notes->comments = $comments;
            

            return response()->json([
                'success' => true,
                'message' => 'Message notes fetched successfully!',
                'data' => $notes,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Update chat note comment
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveChatNoteComment(Request $request)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->input('id');
            $connection_id = (int)$request->input('room_id');
            $note_id = (int)$request->input('note_id');
            $message = $request->input('message');

            $notes = MessageNote::where('message_connection_id', '=', $connection_id)->where('id', $note_id);
            if (!$notes->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found!',
                ]);
            }
            $notes = $notes->first();

            if ($id > 0) {
                $comment = MessageNoteComment::where('id', '=', $id)
                    ->where('user_id', '=', $auth_user->id);
                if ($comment->exists()) {
                    $comment = $comment->first();
                } else {
                    $comment = new MessageNoteComment();
                }
            } else {
                $comment = new MessageNoteComment();
            }

            $comment->message_connection_id = $connection_id;
            $comment->user_id = $auth_user->id;
            $comment->note_id = $note_id;
            $comment->message = $message;
            $comment->save();

            $comment_data = $comment;
            $comment_data->date = date('d-m-Y', strtotime($comment->created_at));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Note saved successfully!',
                'data' => $comment_data,
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete chat note comment
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteChatNoteComment($note_id, $id)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();

            $notes = MessageNote::where('id', $note_id);
            if (!$notes->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Note not found!',
                ]);
            }
            $notes = $notes->first();

            $comment = MessageNoteComment::where('id', '=', $id)->where('note_id', $note_id)
                ->where('user_id', '=', $auth_user->id);

            
            if ($comment->exists()) {
                $comment->delete();
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Note Comment deleted successfully!',
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
