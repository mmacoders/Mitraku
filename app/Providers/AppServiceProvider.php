<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\GmailService;
use App\Notifications\Channels\GmailChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;

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
        Vite::prefetch(concurrency: 3);
        
        // Register custom Gmail notification channel
        Notification::extend('gmail', function ($app) {
            return new GmailChannel(new GmailService());
        });
    }
}