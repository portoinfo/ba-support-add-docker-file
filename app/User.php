<?php

namespace App;

use App\Models\Company;
use App\Models\Company_user;
use App\Models\Subsidiary;
use App\Tools\Builderall\BuilderallAccount;
use App\Tools\Crypt;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'user_auth';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'terms_user', 
        'is_anonymous', 'cookies_accepted', 'password', 
        'is_admin', 'subsidiary_id', 'language', 'hash_code', 
        'api_token', 'telegram', 'config'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getMamaAttribute($value)
    {
        return $value;
    }

    /**
     * Create or Update user with Unified Registration data
     * @param array $data
     * @return App\User
     */
    public static function createOrUpdateOfficeUser($data)
    {
        $sub  = Subsidiary::findByLocale($data['locale']);
        $user = self::where('user_uuid', $data['uuid'])->first();

        if (!$user)
        {
            $user = self::firstOrNew(['email' => $data['email']]);
        }

        $user->email     = $data['email'];
        $user->name      = $data['name'];
        $user->language  = str_replace('-', '_', $data['locale']);
        $user->can_create_company = true;
        $user->password  = $user->password ?? bcrypt(Str::random(30));
        $user->hash_code = $user->hash_code ?? Crypt::encrypt($data['email']);
        $user->subsidiary_id = $sub->id;
        $user->user_uuid = $data['uuid'];

        $user->save();
        $user->fresh();

        // update account according to Office data
        BuilderallAccount::updateUserAccount($user);

        return $user;
    }

    /**
     * Get the companies wich user is teh owner
     */
    public function getOwnCompanies(string $status = null)
    {
        $companies_id = Company_user::select('company_id')
            ->where('user_auth_id', $this->id)
            ->where('is_admin', true)->get()->toArray();

        $select = Company::whereIn('id', $companies_id);

        if ($status)
        {
            $select->where('status', $status);
        }

        return $select->get();
    }

    /**
     * Return if user is the company owner
     * @param  int $company_id
     * @return bool
     */
    public function isCompanyOwner(int $company_id)
    {
        return Company_user::where('company_id', $company_id)
            ->where('user_auth_id', $this->id)
            ->where('is_admin', true)
            ->exists();
    }

    /**
     * Return if user is part of the company owner
     * @param  int $company_id
     * @return bool
     */
    public function isPartOfCompany (int $company_id)
    {
        return Company_user::where('company_id', $company_id)
            ->where('user_auth_id', $this->id)
            ->exists();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
