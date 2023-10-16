<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class telegramUser extends Model
{
    // use HasFactory;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'usuario' 
    ];
}
