<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionHistory extends Model
{
    use HasFactory;
    protected $table = "mention_histories";
    protected $primaryKey = "id";
}
