<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTask extends Model
{
    use HasFactory;

    protected $table = "messages_tasks";
    protected $primaryKey = "id";
}
