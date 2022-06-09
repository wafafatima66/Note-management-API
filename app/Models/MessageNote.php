<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageNote extends Model
{
    use HasFactory;

    protected $table = "message_notes";
    protected $primaryKey = "id";
}
