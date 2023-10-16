<?php

namespace App\Tools\Builderall;

use App\Exceptions\OperationNotAllowedException;
use App\Exceptions\UserNotFoundException;
use App\Jobs\HelpdeskContentControlJob;
use App\LogBuilderallStatus;
use App\User;

class BuilderallAccount
{
    /**
     * Token to execute routines
     */
    const ROUTINE_TOKEN = '50941bf460efcb1356249a2e5018f8c8';

    /**
     * Status to active
     */
    const INACTIVE = 'INACTIVE';

    /**
     * Status to inactive
     */
    const ACTIVE   = 'ACTIVE';

    /**
     * Update BA HELPDESK account with Office data
     * @param App/User $user
     * 
     * @return void
     */
    public static function updateUserAccount(User $user)
    {
        if($user->user_uuid)
        {
            $office = new Office();
            $data   = $office->getUserData($user->user_uuid);
            
            if (!empty($data))
            {
                $new_status = $data['is_active'] ? self::ACTIVE : self::INACTIVE;

                if (!$data['tool_is_available'])
                {
                    $new_status = self::INACTIVE;
                }

                self::updateAllStatus($user, $new_status, 'SYSTEM');
            }
        }
    }

    /**
     * Update user companies according to status
     * @param App/User $user
     * @return void
     */
    public static function updateUserCompaniesStatus(User $user, string $status)
    {   
        /**
         * Get all companies that status is wrong
         */
        $s_status  = $status == self::INACTIVE ? self::ACTIVE : self::INACTIVE;
        $companies = $user->getOwnCompanies($s_status);

        foreach ($companies as $company) 
        {
            $company->status = $status;
            $company->save();
        }
    }
    
    /**
     * Activate or Inactivate user
     * @param string $uuid
     * @param string $token
     * @param string $operation
     * 
     * @throws App\Exceptions\OperationNotAllowedException
     * @throws App\Exceptions\UserNotFoundException;
     * 
     * @return void
     */
    public static function activeOrInactivateAccount(string $uuid, string $token, string $operation)
    {
        if ($token != self::ROUTINE_TOKEN)
        {
            throw new OperationNotAllowedException();
        }
        
        $user = User::where('user_uuid', $uuid)->first();
        if (!$user)
        {   
            /**
             * Retornando para evitar erro nos casos em que o usuário não se encontra cadastrado no sistema.
             */
            return;
            // throw new UserNotFoundException();
        }
        
        HelpdeskContentControlJob::dispatch($user, $operation);
    }

    /**
     * Execute all operations to change user status
     * @param App\User $user
     * @param string $status
     * 
     * @return void
     */
    public static function updateAllStatus(User $user, string $status, string $origin = 'SYSTEM')
    {
        $old_status = $user->builderall_status;

        self::updateUserCompaniesStatus($user, $status);

        if ($user->builderall_status != $status)
        {
            $user->builderall_status = $status;
            $user->save();
        }

        LogBuilderallStatus::create([
            'user_auth_id' => $user->id,
            'old_status'   => $old_status ?? $status,
            'new_status'   => $status,
            'origin'       => $origin
        ]);
    }
}