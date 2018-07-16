<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // All views must be notified of any new bid count since last login
        view()->composer('*', 'App\ViewComposers\BidCountComposer');
    }

    public function register()
    {

    }
}
