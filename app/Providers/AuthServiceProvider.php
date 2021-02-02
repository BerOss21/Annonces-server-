<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Models\Annoucement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('edit-annoucement', function (User $user, Annoucement $annoucement) {
            return ($user->id === $annoucement->user_id || $user->role=="admin");
        });
        Gate::define('edit-category', function (User $user) {
            return $user->role="admin"
                        ? Response::allow()
                        : Response::deny('You must be an administrator.');
        });

        Passport::routes();
    }
}
