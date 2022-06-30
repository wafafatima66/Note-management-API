<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocuaNoteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'user_id'
    ];

   
   
}
