<?php

namespace Walladog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Walladog\SiteComment;
use Walladog\User;

class SiteCommentsPolicy
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

    public function destroy(User $user, SiteComment $comment)
    {
        if ($user->id == $comment->user_id) {
            return true;
        }
    }
}
