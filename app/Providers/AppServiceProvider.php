<?php

namespace App\Providers;

use App\Mail\GmailOAuthTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendWelcomeNotification;
use Illuminate\Auth\Events\Verified;
use App\Listeners\SendVerifiedEmail;

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

        // Push the TrackUserActivity middleware into the web group so every
        // authenticated request updates presence information in cache.
        if ($this->app->bound('router')) {
            $this->app['router']->pushMiddlewareToGroup('web', \App\Http\Middleware\TrackUserActivity::class);
        }

        // Listen for Registered event and send welcome notification
        Event::listen(Registered::class, SendWelcomeNotification::class);
        // Listen for Verified event and send account verified email
        Event::listen(Verified::class, SendVerifiedEmail::class);
    }
}
