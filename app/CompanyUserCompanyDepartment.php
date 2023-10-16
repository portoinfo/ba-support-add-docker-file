<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CompanyUserCompanyDepartment extends Model
{
    use Notifiable;
    protected $table = 'company_user_company_department';
    
    protected $fillable = [
        'company_user_id', 'company_department_id'
    ];

    public function chat(){
        return $this->belongsTo(CompanyUserCompanyDepartment::class);
    }
}
