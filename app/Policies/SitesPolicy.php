<?php

namespace Walladog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Walladog\Site;
use Walladog\User;

class SitesPolicy
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

    public function update(User $user, Site $site)
    {
        if ($user->id == $site->user_id && $site->deleted == 0) {
            return true;
        }
    }

    public function destroy(User $user, Site $site)
    {
        if ($user->id == $site->user_id) {
            return true;
        }
    }
}
