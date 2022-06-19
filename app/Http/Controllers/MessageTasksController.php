<?php

namespace App\Http\Controllers;

use App\Models\MessageTask;
use App\Models\MessageTaskStatus;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;

class MessageTasksController extends Controller
{
    /**
     * Get task status list
     * @param $room_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusList($room_id, Request $request)
    {
        try {
            $search = (string)$request->input('search');
            $status_list = MessageTaskStatus::where('connection_id', '=', $room_id)
                ->when(trim($search) !== "", function ($q) use ($search) {
                    return $q->where('title', 'LIKE', '%' . $search . '%');
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Task status list fetched successfully!',
                'data' => $status_list,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Create or save a task status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTaskStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = (int)$request->input('id');
            $room_id = (int)$request->input('room_id');
            $title = (string)$request->input('title');
            $icon = (string)$request->input('icon');
            $color = (string)$request->input('color');

            if (!$room_id > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid room id!',
                ]);
            }

            $status = MessageTaskStatus::where('id', '=', $id);

            if ($status->exists()) {
                $status = $status->first();
            } else {
                $status = new MessageTaskStatus();
            }

            $status->connection_id = $room_id;
            $status->title = $title;
            $status->icon = $icon;
            $status->color = $color;
            $status->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task status saved successfully!',
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
     * Get task list
     * @param $room_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskList($room_id, Request $request)
    {
        try {
            $room_id = (int)$room_id;
            $status_id = (int)$request->input('status_id');
            $search = (string)$request->input('search');

            if (!$room_id > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid room id!',
                ]);
            }

            $tasks = MessageTaskStatus::where('connection_id', '=', $room_id)
                ->when($status_id > 0, function ($q) use ($status_id) {
                    return $q->where('status_id', $status_id);
                })->when(trim($search) !== "", function ($q) use ($search) {
                    return $q->where('title', 'LIKE', '%' . $search . '%');
                });

            return response()->json([
                'success' => true,
                'message' => 'Message tasks fetched successfully!',
                'data' => $tasks,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Create or save a task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTask(Request $request)
    {
        try {
            DB::beginTransaction();
            $id = (int)$request->input('id');
            $room_id = (int)$request->input('room_id');
            $parent_id = (int)$request->input('parent_id');
            $assignee_id = (int)$request->input('assignee_id');
            $status_id = (int)$request->input('status_id');
            $type = (string)$request->input('type');
            $title = (string)$request->input('title');
            $description = (string)$request->input('description');
            $deadline = (string)$request->input('deadline');

            if (!$room_id > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid room id!',
                ]);
            }

            $task = MessageTask::where('id', '=', $id)
                ->where('connection_id', $room_id);

            if ($task->exists()) {
                $task = $task->first();
                $task->updated_at = date('Y-m-d H:i:s');
            } else {
                $task = new MessageTask();
            }

            $task->connection_id = $room_id;

            if ($assignee_id > 0) {
                $task->assignee_id = $assignee_id;
            }

            if ($status_id > 0) {
                $task->status_id = $status_id;
            }

            if ($parent_id > 0) {
                $task->parent_id = $parent_id;
            }

            if ($type !== "") {
                $task->type = $type;
            }

            if ($title !== "") {
                $task->title = $title;
            }

            if ($description !== "") {
                $task->description = $description;
            }

            if ($deadline !== "") {
                $task->deadline = date('Y-m-d h:i:s', strtotime($deadline));
            }

            $task->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task saved successfully!',
                'data' => $task,
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
