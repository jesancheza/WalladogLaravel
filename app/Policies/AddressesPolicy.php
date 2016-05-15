<?php

namespace Walladog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Walladog\Address;
use Walladog\User;

class AddressesPolicy
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
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function destroy(User $user, Address $address)
    {
        if ($user->id == $address->user_id || $user->id == $address->site->user_id | $user->id == $address->partner_id->user_id) {
            return true;
        }
    }
}
