<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\FolderManagement;
use App\Models\FileManagement;
use App\Models\MessageConnection;
use App\Models\MessageConnectionUser;

class FolderManagementController extends Controller
{
    
    /**
     * Get all the  folder contents, the children folders and files
     * @param $connection_id
     * @param $folder_id is optional
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFolder($connection_id, $folder_id=null){

        try {
            $auth_user = auth()->user();
            $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '=', $auth_user->id);
            if (!$connection_users->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid connection!',
                ]);
            }
            $folders = FolderManagement::where('connection_id', $connection_id);
            $files = FileManagement::where('connection_id', $connection_id);
            if(!$folder_id){
                $folders = $folders->where('folder_parent_id',  0)->get();
                $files = $files->where('folder_id',  0)->get();
                $folder_info = [];
            }else{
                $folders = $folders->where('folder_parent_id',  $folder_id)->get();
                $files = $files->where('folder_id', $folder_id)->get();
                $folder_info = FolderManagement::where('connection_id', $connection_id)->where('id', $folder_id)->first();
            }

            foreach ($folders as $folder) {
                $folders->date = date('d-m-Y', strtotime($folder->created_at));
            }
            foreach ($files as $file) {
                $file->date = date('d-m-Y', strtotime($file->created_at));
            }

            $data = [
                'folder_info'=>$folder_info,
                'folders'=>$folders,
                'files'=>$files
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Folders fetched successfully!',
                'data' => $data,
            ]);
        
        }catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    
    /**
     * Update and save folder
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveFolder(Request $request){

        try{
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->id;
            $folder_parent_id = (int)$request->folder_parent_id;
            $connection_id = (int)$request->room_id;
            $categoryid = (int)$request->category_id;
            $folder_name = str_replace( ",", "-"    ,$request->folder_name);


            $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '=', $auth_user->id);
            if (!$connection_users->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid connection!',
                ]);
            }

            if ($id > 0) {
                $folder = FolderManagement::where('id', '=', $id)
                    ->where('connection_id', '=', $connection_id);
                if ($folder->exists()) {
                    $folder = $folder->first();
                } else {
                    $folder = new FolderManagement;
                    $folder->connection_id = $connection_id;
                    $folder->folder_parent_id = $folder_parent_id ;
                    $folder->folder_creator_id = $auth_user->id;
                    $folder->save();

                }
            } else {
                $folder = new FolderManagement;
                $folder->connection_id = $connection_id;
                $folder->folder_parent_id = $folder_parent_id ;
                $folder->folder_creator_id = $auth_user->id;
                $folder->save();
            }
            $folder->category_id = $categoryid;

            $folder_url = config('app.url') . "/api/v1/private/folders/". $connection_id . "/". $folder->id;
            if($folder_parent_id > 0){
                $folder_parent = FolderManagement::where('connection_id', $connection_id)->where('id', $folder_parent_id)->first();
                $folder->folder_path = $folder_parent->folder_path . "," . $folder_name;
                $folder->folder_path_url = $folder_parent->folder_path_url . "," . $folder_url;
            }else{
                $folder->folder_path = $folder_name;
                $folder->folder_path_url = $folder_url;
            }

            $folder->folder_name = $folder_name;
            $folder->folder_url = $folder_url;

            $folder->save();
            $folder_data = $folder;
            $folder_data->date = date('d-m-Y', strtotime($folder->created_at));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Folder saved successfully!',
                'data' => $folder_data,
            ]);
            
        }catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }


    public function deleteFolder($connection_id, $folder_id){

        try{
            DB::beginTransaction();
            $auth_user = auth()->user();
            $folder_parent_id = $folder_id;

            $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '=', $auth_user->id);
            if (!$connection_users->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid connection!',
                ]);
            }

            $folder = FolderManagement::where('id', $folder_id)->where('connection_id', $connection_id);
            if(!$folder->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found!',
                ]);
            }

            $folder = $folder->first();
            $child_folders = FolderManagement::where('connection_id', $connection_id)->where('id', '<>', $folder_id)->where('folder_path', 'like', $folder->folder_path.'%')->where('folder_path_url', 'like', $folder->folder_path_url.'%')->get();
            
            foreach($child_folders AS $value){
                $files = FileManagement::where('folder_id', $value->id);
                if($files->exists()){
                    $files = $files->get();
                    foreach($files as $v){
                        Storage::delete('/public/uploads/'. $v->file_url);
                    }
                }
                FileManagement::where('folder_id', $value->id)->delete();
            }

            $this_folder_files = FileManagement::where('folder_id', $folder->id);
            if($this_folder_files->exists()){
                $this_folder_files = $this_folder_files->get();
                foreach($this_folder_files as $v){
                    Storage::delete('/public/uploads/'. $v->file_url);
                    $v->delete();
                }
            }
            FolderManagement::where('connection_id', $connection_id)->where('id', '<>', $folder_id)->where('folder_path', 'like', $folder->folder_path.'%')->where('folder_path_url', 'like', $folder->folder_path_url.'%')->delete();
            $folder->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Folder deleted successfully!'
            ]);

        }catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }

}
