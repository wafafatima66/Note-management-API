<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocuaNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'category_id',
        'user_id',
        'title'
    ];

}
