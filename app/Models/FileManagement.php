<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagement extends Model
{
    use HasFactory;
    protected $table = "file_managements";
    protected $primaryKey = "id";

    public function connection()
    {
        return $this->belongsTo(MessageConnection::class, 'connection_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'folder_creator_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(User::class, 'category_id', 'id');
    }
}
