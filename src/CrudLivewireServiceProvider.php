<?php

namespace Medrachy\CrudLivewire;

use Illuminate\Support\ServiceProvider;
use Medrachy\CrudLivewire\Commands\BuildCrudCommand;

class CrudLivewireServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the command 
        if ($this->app->runningInConsole()) {
            $this->commands([
                BuildCrudCommand::class
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
