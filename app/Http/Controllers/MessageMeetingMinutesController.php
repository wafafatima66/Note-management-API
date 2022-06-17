<?php

namespace App\Http\Controllers;

use App\Models\MessageMeetingMinute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Exception;

class MessageMeetingMinutesController extends Controller
{
    /**
     * Get all the chat meeting minutes
     * @param $connection_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMeetingMinutes($connection_id, Request $request)
    {
        try {
            $auth_user = auth()->user();
            $search = (string)$request->input('search');
            $notes = MessageMeetingMinute::where('message_connection_id', '=', $connection_id)
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
                'message' => 'Message meeting minutes fetched successfully!',
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
     * Update chat meeting minute
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveMeetingMinute(Request $request)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->input('id');
            $connection_id = (int)$request->input('room_id');
            $title = $request->input('title');
            $description = $request->input('description');

            if ($id > 0) {
                $note = MessageMeetingMinute::where('id', '=', $id)
                    ->where('user_id', '=', $auth_user->id);
                if ($note->exists()) {
                    $note = $note->first();
                } else {
                    $note = new MessageMeetingMinute();
                }
            } else {
                $note = new MessageMeetingMinute();
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
                'message' => 'Meeting minute saved successfully!',
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
     * Delete chat meeting minute
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMeetingMinute($id)
    {
        try {
            DB::beginTransaction();
            $auth_user = auth()->user();

            $note = MessageMeetingMinute::where('id', '=', $id)
                ->where('user_id', '=', $auth_user->id);

            if ($note->exists()) {
                $note->delete();
                DB::commit();
            }

            return response()->json([
                'success' => true,
                'message' => 'Meeting minute deleted successfully!',
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
