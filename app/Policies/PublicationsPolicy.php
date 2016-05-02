<?php

namespace Walladog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Walladog\Publication;
use Walladog\User;

class PublicationsPolicy
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

    public function update(User $user, Publication $publication)
    {
        if ($user->id == $publication->user_id && $publication->deleted == 0) {
            return true;
        }
    }

    public function destroy(User $user, Publication $publication)
    {
        if ($user->id == $publication->user_id) {
            return true;
        }
    }
}
