<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageNoteComment extends Model
{
    use HasFactory;

    protected $table = "message_note_comments";
    protected $primaryKey = "id";

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
