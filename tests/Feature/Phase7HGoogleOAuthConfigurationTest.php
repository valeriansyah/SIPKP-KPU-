<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class Phase7HGoogleOAuthConfigurationTest extends TestCase
{
    public function test_google_oauth_routes_exist()
    {
        $this->assertTrue(Route::has('auth.google.redirect'));
        $this->assertTrue(Route::has('auth.google.callback'));
    }

    public function test_google_oauth_config_is_populated()
    {
        $clientId = Config::get('services.google.client_id');
        $clientSecret = Config::get('services.google.client_secret');
        $redirectUri = Config::get('services.google.redirect');

        $this->assertNotEmpty($clientId, 'GOOGLE_CLIENT_ID is not configured in .env');
        $this->assertNotEmpty($clientSecret, 'GOOGLE_CLIENT_SECRET is not configured in .env');
        $this->assertNotEmpty($redirectUri, 'GOOGLE_REDIRECT_URI is not configured in .env');
    }

    public function test_socialite_driver_can_be_built()
    {
        // Socialite driver requires services.google to be properly set
        // If it throws an exception, it means Socialite is either not installed or improperly configured.
        $driver = Socialite::driver('google');
        $this->assertNotNull($driver);
    }
}
