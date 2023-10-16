<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_group extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'user_group';

	protected $dates = ['deleted_at'];
}
