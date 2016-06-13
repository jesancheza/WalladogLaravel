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

    public function update(User $user, Address $address)
    {
        if (($user->id == $address->user_id
                || (isset($address->site) && $user->id == $address->site->user_id)
                || (isset($address->partner) && $user->id == $address->partner->user_id)) && $address->deleted == 0) {
            return true;
        }
    }

    public function destroy(User $user, Address $address)
    {
        if ($user->id == $address->user_id || (isset($address->site) && $user->id == $address->site->user_id) |
            $user->id == (isset($address->partner_id) && $address->partner_id->user_id)) {
            return true;
        }
    }
}
