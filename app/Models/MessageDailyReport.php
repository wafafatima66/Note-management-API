<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageDailyReport extends Model
{
    use HasFactory;

    protected $table = "message_daily_reports";
    protected $primaryKey = "id";
}
