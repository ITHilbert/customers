<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests;

use Illuminate\Foundation\Application;
use ITHilbert\Customer\CustomerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Yajra\DataTables\DataTablesServiceProvider;

/**
 * Basis aller Paket-Tests: eigene Testbench-App, unabhängig vom Host.
 */
abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        $provider = [CustomerServiceProvider::class];

        // Der Controller nutzt die DataTables-Fassade; Provider nur, wenn das Paket installiert ist
        if (class_exists(DataTablesServiceProvider::class)) {
            $provider[] = DataTablesServiceProvider::class;
        }

        return $provider;
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('queue.default', 'sync');
        // Host-Views, die das Paket voraussetzt, als Stubs: layouts.master, include.message und
        // layouts.customer (die Views erweitern 'layouts.customer', das Paket liefert es nur per vendor:publish)
        $app['config']->set('view.paths', [__DIR__.'/Fixtures/views']);
    }

    /**
     * Legt die Paket-Tabellen an.
     */
    protected function migrieren(): void
    {
        $this->artisan('migrate')->assertSuccessful();
    }
}
