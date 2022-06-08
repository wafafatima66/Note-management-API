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
}
