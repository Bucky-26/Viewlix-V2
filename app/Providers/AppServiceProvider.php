<?php

namespace App\Providers;

use App\Mail\GmailOAuthTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if (config('app.env') === 'local') {
            URL::forceScheme('https');
        }

        // Register Gmail OAuth2 mail transport
        Mail::extend('gmail-oauth', function () {
            return new GmailOAuthTransport(
                config('services.google.client_id'),
                config('services.google.client_secret'),
                config('services.google.refresh_token'),
                config('services.google.email')
            );
        });
    }
}
