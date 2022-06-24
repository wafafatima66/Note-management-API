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

class FileManagementController extends Controller
{
    public function saveFile(Request $request){

        try{

            if (!$request->hasfile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not uploaded!',
                ]);
            }

            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$request->id;
            $folder_id =  (int)$request->folder_id  ;
            $connection_id = (int)$request->room_id;
            $categoryid = (int)$request->category_id;
            $uploaded_file = $request->file('file');


            $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '=', $auth_user->id);
            if (!$connection_users->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid connection!',
                ]);
            }

            if ($id > 0) {
                $file = FolderManagement::where('id', '=', $id)
                    ->where('connection_id', '=', $connection_id);
                if ($file->exists()) {
                    $file = $folder->first();
                } else {
                    $file = new FileManagement;
                    $file->connection_id = $connection_id;
                    $file->folder_id = $folder_id ;
                    $file->uploader_id = $auth_user->id;
                    $file->save();

                }
            } else {
                $file = new FileManagement;
                $file->connection_id = $connection_id;
                $file->folder_id = $folder_id ;
                $file->uploader_id = $auth_user->id;
                $file->save();
            }
            $file->category_id = $categoryid;
            $baseFolderName = '/file_managements/';
            $original_name = $uploaded_file->getClientOriginalName();
            $_file = md5("folder" . $file->folder_id . $uploaded_file->getClientOriginalName() . time()) . "." . $uploaded_file->getClientOriginalExtension();
            $_file = $baseFolderName . $_file;
            Storage::disk('local')->put('/public/uploads/' . $_file, file_get_contents($uploaded_file));

            $file->file_name = $original_name;
            $file->file_url = $_file;

            $file->save();
            $file_data = $file;
            $file_data->date = date('d-m-Y', strtotime($file->created_at));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File saved successfully!',
                'data' => $file_data,
            ]);
            
        }catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }

    public function deleteFile($connection_id,$id){

        try{
            DB::beginTransaction();
            $auth_user = auth()->user();
            $id = (int)$id;

            $connection_users = MessageConnectionUser::where('connection_id', '=', $connection_id)
                    ->where('user_id', '=', $auth_user->id);
            if (!$connection_users->exists()){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid connection!',
                ]);
            }
            $files = FileManagement::where('id', $id);
            if($files->exists()){
                
                $files = $files->first();
                Storage::delete('/public/uploads/'. $files->file_url);
                $files->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!',
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
