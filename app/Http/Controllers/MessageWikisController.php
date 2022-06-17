<?php

namespace App\Http\Controllers;

use App\Models\MessageWiki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Exception;

class MessageWikisController extends Controller
{
    /**
     * Get all the chat wikis
     * @param $connection_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWikis($connection_id, Request $request)
    {
        try {
            $auth_user = auth()->user();
            $search = (string)$request->input('search');
            $notes = MessageWiki::where('message_connection_id', '=', $connection_id)
                ->where('user_id', '=', $auth_user->id)
                ->when(trim($search) !== "", function ($q) use ($search) {
                    return $q->where('title', 'LIKE', '%' . $search . '%');
                })
                ->orderBy('id', 'desc')->get();

            foreach ($notes as $note) {
                $note->date = date('d-m-Y', strtotime($note->created_at));
            }

            return response()->json([
                'success' => true,
                'message' => 'Message wikis fetched successfully!',
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
     * Update chat wiki
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveWiki(Request $request)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->input('id');
            $connection_id = (int)$request->input('room_id');
            $title = $request->input('title');
            $description = $request->input('description');

            if ($id > 0) {
                $note = MessageWiki::where('id', '=', $id)
                    ->where('user_id', '=', $auth_user->id);
                if ($note->exists()) {
                    $note = $note->first();
                } else {
                    $note = new MessageWiki();
                }
            } else {
                $note = new MessageWiki();
            }

            $note->message_connection_id = $connection_id;
            $note->user_id = $auth_user->id;
            $note->title = $title;
            $note->description = $description;
            $note->save();

            $note_data = $note;
            $note_data->date = date('d-m-Y', strtotime($note->created_at));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Meeting wiki saved successfully!',
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
     * Delete chat wiki
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteWiki($id)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();

            $note = MessageWiki::where('id', '=', $id)
                ->where('user_id', '=', $auth_user->id);

            if ($note->exists()) {
                $note->delete();
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Meeting wiki deleted successfully!',
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
