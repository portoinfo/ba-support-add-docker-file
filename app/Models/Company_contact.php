<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company_contact extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'company_contact';

	protected $dates = ['deleted_at'];

}
