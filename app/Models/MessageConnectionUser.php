<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageConnectionUser extends Model
{
    use HasFactory;

    protected $table = "message_connection_users";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function connection()
    {
        return $this->belongsTo(MessageConnection::class, 'connection_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
