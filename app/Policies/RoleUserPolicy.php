<?php

namespace App\Policies;

use App\Models\RoleUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class RoleUserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function roles(User $user, RoleUser $role){
        {
            if($user->id == $role->user_id && $role->rol == "admin"){
                return true;
            }
            return false;
    }
    }
}
