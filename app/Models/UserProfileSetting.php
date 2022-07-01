<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileSetting extends Model
{
    use HasFactory;
    protected $table = "user_profile_settings";
    protected $primaryKey = "id";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
