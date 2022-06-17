<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageWiki extends Model
{
    use HasFactory;

    protected $table = "message_wikis";
    protected $primaryKey = "id";
}
