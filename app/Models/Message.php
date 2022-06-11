<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = "messages";
    protected $primaryKey = "id";

    public function sender() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function attachments() {
        return $this->hasMany(MessageAttachment::class, 'message_id', 'id');
    }
}
