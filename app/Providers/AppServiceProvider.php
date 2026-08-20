<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (!$user->is_active || (method_exists($user, 'trashed') && $user->trashed())) {
                return false;
            }
        });

        Gate::define('manage-master-data', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'operator';
        });

        Gate::define('manage-sub-operator', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'operator';
        });

        Gate::define('view-audit-log', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'operator';
        });

        Gate::define('view-all-reports', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'operator';
        });

        Gate::define('verify-report', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'sub_operator';
        });

        Gate::define('create-report', function ($user) {
            return Str::slug($user->role->role_name, '_') === 'pelapor';
        });
    }
}
