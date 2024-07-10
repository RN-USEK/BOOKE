<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BookInteractionService;

class BookInteractionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BookInteractionService::class, function ($app) {
            return new BookInteractionService();
        });
    }

    public function boot()
    {
        //
    }
}