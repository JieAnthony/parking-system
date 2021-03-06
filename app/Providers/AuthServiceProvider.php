<?php

namespace App\Providers;

use App\Auth\CacheEloquentUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('cache-user', function () {
            return resolve(CacheEloquentUserProvider::class);
        });

        Gate::guessPolicyNamesUsing(function ($class) {
            return '\\App\\Policies\\'.class_basename($class).'Policy';
        });
    }
}
