<?php

namespace Walladog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Walladog\Pet;
use Walladog\User;

class PetsPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        //TODO: implementar deleted si se cree oportuno
        /*
        if($user->deleted==1){
            return false;
        }
        */
    }

    public function destroy(User $user, Pet $pet)
    {
        if ($user->id == $pet->user_id || (isset($pet->partner) && $user->id == $pet->partner->user_id)) {
            return true;
        }
    }
}
