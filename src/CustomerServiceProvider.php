<?php

namespace ITHilbert\Customer;

use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->registerRoutes();
        $this->publishAssets();

        //Commands Registrieren
        $this->commands( \ITHilbert\Customer\App\Console\Commands\CustomerCopyFiles::class );
        $this->commands( \ITHilbert\Customer\App\Console\Commands\CustomerInstall::class );
    }


    public function publishAssets()
    {
        $this->publishes([
            __DIR__ .'/Resources/assets' => public_path('vendor/customer'),
        ]);
    }

    /**
     * Register Routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }


    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ .'/Config/config.php' => config_path('customer.php'),
        ]);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $this->publishes([
            __DIR__ .'/Resources/views' => resource_path('views/vendor/customer'),
            __DIR__ .'/Resources/views/layouts/customer.blade.php' => resource_path('views/layouts/customer.blade.php'),
        ]);

        $this->loadViewsFrom(resource_path('Resources/views/vendor/customer'), 'customer');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $this->publishes([
            __DIR__.'/Resources/lang' => resource_path('lang/vendor/customer'),
        ]);

        $this->loadTranslationsFrom( resource_path('/Resources/lang/vendor/customer'), 'customer');
    }



}
