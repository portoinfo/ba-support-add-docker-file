<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company_department extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'company_department';

	protected $dates = ['deleted_at'];

	public function settings()
	{
		return $this->hasOne(CompanyDepartmentSettings::class, 'company_department_id', 'id');
	}

	/**
	 * Check if department is allowed to send notification
	 * @param string $type email|office_notification
	 * @return boolean
	 */
	public function ticketNotificationsSettings(string $type)
	{
		$settings = $this->settings->settings;
		if (!$settings)
		{
			return false;
		}

		$settings = json_decode($settings, true);
		
		if (isset($settings['ticket']))
		{
			if ($type == 'email')
			{
				if (isset($settings['ticket']['sendEmail']))
				{
					return !empty($settings['ticket']['sendEmail']) ? $settings['ticket']['sendEmail'] : false;
				}
			}
			else if ($type == 'office_notification')
			{
				return !empty($settings['ticket']['notificationsOficce']) ? $settings['ticket']['notificationsOficce'] : false;
			}
		}

		return false;
	}
}
