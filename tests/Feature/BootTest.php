<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests\Feature;

use Composer\Autoload\ClassLoader;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use ITHilbert\Customer\CustomerServiceProvider;
use ITHilbert\Customer\Database\Seeders\CustomerDatabaseSeeder;
use ITHilbert\Customer\Tests\TestCase;

/**
 * Charakterisierung des Boot-Verhaltens (Ist-Stand, keine Spec).
 */
final class BootTest extends TestCase
{
    // Charakterisierung
    public function test_provider_ist_registriert(): void
    {
        $this->assertArrayHasKey(CustomerServiceProvider::class, $this->app->getLoadedProviders());
    }

    // Charakterisierung: Config, Views, Lang und Assets werden nur veröffentlicht; Config wird nicht zusammengeführt
    public function test_publish_pfade_sind_registriert_und_config_nicht_zusammengefuehrt(): void
    {
        $pfade = ServiceProvider::pathsToPublish(CustomerServiceProvider::class);

        $this->assertContains(config_path('customer.php'), $pfade);
        $this->assertContains(resource_path('views/vendor/customer'), $pfade);
        $this->assertContains(resource_path('views/layouts/customer.blade.php'), $pfade);
        $this->assertContains(resource_path('lang/vendor/customer'), $pfade);
        $this->assertContains(public_path('vendor/customer'), $pfade);
        $this->assertNull(config('customer.hourly_rate'));
    }

    // Charakterisierung
    public function test_config_vorlage_enthaelt_standardwerte(): void
    {
        $config = require __DIR__.'/../../src/Config/config.php';

        $this->assertSame('customer', $config['name']);
        $this->assertSame('760', $config['hourly_rate']);
        $this->assertSame('1', $config['mileage_allowance']);
    }

    // Charakterisierung: View-Namespace "customer"
    public function test_views_sind_unter_customer_namespace_registriert(): void
    {
        foreach (['index', 'create', 'edit', 'show'] as $view) {
            $this->assertTrue(view()->exists('customer::customer.'.$view), $view);
        }
        $this->assertTrue(view()->exists('customer::layouts.customer'));
    }

    // Charakterisierung: Migrationen legen vier Tabellen an
    public function test_migrationen_legen_erwartete_tabellen_an(): void
    {
        $this->artisan('migrate')->assertSuccessful();

        foreach (['customers', 'customer_types', 'invoice_types', 'name_address'] as $tabelle) {
            $this->assertTrue(Schema::hasTable($tabelle), $tabelle);
        }
        $this->assertTrue(Schema::hasColumns('customers', ['customer_type_id', 'company_row_1', 'email', 'hourly_rate', 'edit_user_id', 'deleted_at', 'created_at']));
    }

    // Charakterisierung: Rollback entfernt die Tabellen wieder
    public function test_migrationen_lassen_sich_zuruecknehmen(): void
    {
        $this->artisan('migrate')->assertSuccessful();
        $this->artisan('migrate:rollback')->assertSuccessful();

        $this->assertFalse(Schema::hasTable('customers'));
        $this->assertFalse(Schema::hasTable('name_address'));
    }

    // Charakterisierung
    public function test_artisan_befehle_sind_registriert(): void
    {
        $befehle = array_keys($this->app->make(Kernel::class)->all());

        $this->assertContains('customer:copyfiles', $befehle);
        $this->assertContains('customer:install', $befehle);
    }

    // Charakterisierung: neun Web-Routen mit Namen, Methoden und Middleware web + auth
    public function test_routen_sind_mit_namen_und_methoden_registriert(): void
    {
        $erwartet = [
            'customer.index' => ['customer/index', ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], true],
            'customer.list' => ['customer/list', ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], true],
            'customer.create' => ['customer/create', ['GET', 'HEAD'], false],
            'customer.store' => ['customer/store', ['POST'], false],
            'customer.edit' => ['customer/edit/{customerID}', ['GET', 'HEAD'], false],
            'customer.update' => ['customer/update/{customerID}', ['POST'], false],
            'customer.delete' => ['customer/delete/{customerID}', ['DELETE'], false],
            'customer.show' => ['customer/{customerID}', ['GET', 'HEAD'], false],
        ];

        // Route::any(): Laravel 13 ergänzt QUERY, daher dort nur Teilmenge prüfen
        foreach ($erwartet as $name => [$uri, $methoden, $any]) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertSame($uri, $route->uri(), $name);
            if ($any) {
                $this->assertEqualsCanonicalizing($methoden, array_intersect($route->methods(), $methoden), $name);
            } else {
                $this->assertSame($methoden, $route->methods(), $name);
            }
            $this->assertContains('auth', $route->gatherMiddleware(), $name);
            $this->assertContains('web', $route->gatherMiddleware(), $name);
        }
    }

    // Charakterisierung: die API-Routendatei wird nicht geladen
    public function test_api_routen_werden_nicht_geladen(): void
    {
        $this->assertCount(0, array_filter(
            Route::getRoutes()->getRoutes(),
            static fn ($route): bool => $route->uri() === 'api/customer'
        ));
    }

    // Charakterisierung: Seeder legen die Stammdaten an
    public function test_seeder_befuellt_stammdaten(): void
    {
        $this->artisan('migrate')->assertSuccessful();

        $this->artisan('db:seed', ['--class' => CustomerDatabaseSeeder::class])->assertSuccessful();

        $this->assertDatabaseCount('customer_types', 2);
        $this->assertDatabaseHas('customer_types', ['id' => 1, 'customer_type' => 'Firma']);
        $this->assertDatabaseHas('customer_types', ['id' => 2, 'customer_type' => 'Privat']);
        $this->assertDatabaseCount('invoice_types', 3);
        $this->assertDatabaseHas('name_address', ['id' => 1, 'address' => 'Herr']);
        $this->assertDatabaseHas('name_address', ['id' => 2, 'address' => 'Frau']);
    }

    // Regression: Die Entities verwendeten ITHilbert\Vue\Traits\VueComboBox und
    // ITHilbert\Vue\Entities\ComboBox. Diese Klassen existieren nirgends (heute: ITHilbert\LaravelKit\...).
    // Jede Nutzung von Customer, CustomerType, InvoiceType oder NameAddress wirft "Trait not found".
    // Statische Prüfung über den Composer-Loader.
    public function test_alle_externen_klassen_der_entities_sind_aufloesbar(): void
    {
        $loader = null;
        foreach (spl_autoload_functions() as $funktion) {
            if (is_array($funktion) && $funktion[0] instanceof ClassLoader) {
                $loader = $funktion[0];
                break;
            }
        }
        $this->assertNotNull($loader);

        $fehlend = [];
        foreach (glob(__DIR__.'/../../src/Entities/*.php') as $datei) {
            preg_match_all('/^use (ITHilbert\\\\[^;]+);/m', (string) file_get_contents($datei), $treffer);
            foreach ($treffer[1] as $klasse) {
                if ($loader->findFile($klasse) === false) {
                    $fehlend[] = basename($datei).' -> '.$klasse;
                }
            }
        }

        $this->assertSame([], $fehlend);
    }
}
