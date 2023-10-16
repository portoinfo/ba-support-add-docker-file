<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUserClient extends Model
{
    protected $table = 'company_user_client';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_client_id', 'company_id', 'last_login'
    ];
}
