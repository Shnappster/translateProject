<?php

namespace App\Providers;

use App\Contracts\Services\TranslationServiceInterface;
use App\Services\TranslateService;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TranslationServiceInterface::class, TranslateService::class);
    }
}
