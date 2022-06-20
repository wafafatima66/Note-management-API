<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTask extends Model
{
    use HasFactory;

    protected $table = "messages_tasks";
    protected $primaryKey = "id";

    public function assignee() {
        return $this->belongsTo(User::class, 'assignee_id', 'id');
    }

    public function status() {
        return $this->belongsTo(MessageTaskStatus::class, 'status_id', 'id');
    }
}
