<?php

namespace Walladog\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Walladog\Address;
use Walladog\Pet;
use Walladog\Policies\AddressesPolicy;
use Walladog\Policies\PetsPolicy;
use Walladog\Policies\PublicationsPolicy;
use Walladog\Policies\SiteCommentsPolicy;
use Walladog\Policies\SitesPolicy;
use Walladog\Policies\UserPolicy;
use Walladog\Publication;
use Walladog\Site;
use Walladog\SiteComment;
use Walladog\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Walladog\Model' => 'Walladog\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Pet::class => PetsPolicy::class,
        Site::class => SitesPolicy::class,
        Publication::class => PublicationsPolicy::class,
        SiteComment::class => SiteCommentsPolicy::class,
        Address::class => AddressesPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

    }
}
