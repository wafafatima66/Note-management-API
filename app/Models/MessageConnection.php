<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageConnection extends Model
{
    use HasFactory;

    protected $table = "message_connections";
    protected $primaryKey = "id";
}
