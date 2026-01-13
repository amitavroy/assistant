<?php

namespace App\Providers;

use App\Services\Mail\ImapMailService;
use App\Services\Mail\MailServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MailServiceInterface::class, ImapMailService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
