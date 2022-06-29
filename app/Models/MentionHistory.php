<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionHistory extends Model
{
    use HasFactory;
    protected $table = "mention_histories";
    protected $primaryKey = "id";


    /**
     * Get the user that owns the MentionHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
