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

    /**
     * Get all of the mentions for the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentions()
    {
        return $this->hasMany(MentionHistory::class, 'parent_id', 'id')->where('parent_column', 'messages');
    }
}
