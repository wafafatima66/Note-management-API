<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageMeetingMinute extends Model
{
    use HasFactory;

    protected $table = "message_meeting_minutes";
    protected $primaryKey = "id";
}
