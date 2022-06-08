<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageSeenStatus extends Model
{
    use HasFactory;

    protected $table = "message_seen_status";
    protected $primaryKey = "id";
    public $timestamps = false;
}
