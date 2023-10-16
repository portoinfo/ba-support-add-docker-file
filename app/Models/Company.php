<?php

namespace App\Models;

use App\CompanyDomain;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'company';

	protected $dates = ['deleted_at'];

	public function settings()
	{
		return $this->hasOne(CompanySettings::class);
	}

	public function agents()
	{
		$agents = Company_user::where('company_user.company_id', '=', $this->id)
				->whereNull('company_user.deleted_at')
				->join('user_auth', 'user_auth.id', '=', 'company_user.user_auth_id')
				->whereNull('user_auth.deleted_at')
				->get();
		
		return $agents;
	}

	public function companyDomains()
	{
		return $this->hasMany(CompanyDomain::class);
	}
}
