<?php

namespace XBlock\Kernel;

use Illuminate\Support\ServiceProvider;
use XBlock\Access\Service;

class KernelProvider extends ServiceProvider
{


    public function boot()
    {
        $this->app->configure('xblock');
        $this->registerRouter();
    }

    public function register()
    {
        if ($this->app->runningInConsole()) $this->registerMigrations();
        $this->app->singleton('field_dict', function () {
            $register = config('xblock.register.dict', false);
            return $register ? new $register : null;
        });
    }

    protected function registerMigrations()
    {
        Service::loadMigrartion();
        return $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerRouter()
    {
        $this->app->router->group([
            'prefix' => config('xblock.prefix', 'api/xblock'),
            'namespace' => 'XBlock\Kernel', 'middleware' => config('xblock.middleware', 'auth:api')
        ], function ($router) {
            $router->post('/menu', 'KernelController@menu');
            $router->post('/notification', 'KernelController@notification');
            $router->post('/{block}/{action}', 'BlockController@action');
        });
    }

}
