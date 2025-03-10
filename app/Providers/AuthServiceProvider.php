<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport; 

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport::routes(); 
        $this->registerPolicies();

        // Register Passport routes


        Passport::cookie('token');

        
        Passport::tokensExpireIn(Carbon::now()->addDays(15));

        // Set refresh token expiration to 30 days
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    
        // Set personal access token expiration to 1 year (optional)
        Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(3));

        
    }
}
