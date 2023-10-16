<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class telegramMessage extends Model
{
    // use HasFactory;
    public $incrementing = false;
    protected $table = 'telegram_message';

    protected $fillable = [
        'position_message'
        // 'id',
        // 'user_telegram_id',
        // 'mensagem'
    ];
}
