<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'description',
        'url',
        'icon'
    ];
    protected $table = "applications";
    protected $primaryKey = "id";
}
