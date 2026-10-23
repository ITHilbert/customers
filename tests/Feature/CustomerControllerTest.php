<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests\Feature;

use Illuminate\Auth\GenericUser;
use ITHilbert\Customer\Database\Seeders\CustomerDatabaseSeeder;
use ITHilbert\Customer\Entities\Customer;
use ITHilbert\Customer\Tests\Concerns\MitLaravelKitAlias;
use ITHilbert\Customer\Tests\TestCase;
use Yajra\DataTables\Facades\DataTables;

/**
 * Charakterisierung der Kern-Routen (Ist-Stand, keine Spec).
 * Läuft mit dem Behelf-Alias für OFFEN-1; das Host-Layout kommt als Stub aus tests/Fixtures/views.
 */
final class CustomerControllerTest extends TestCase
{
    use MitLaravelKitAlias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setzeLaravelKitAlias();
        $this->migrieren();
        $this->artisan('db:seed', ['--class' => CustomerDatabaseSeeder::class])->assertSuccessful();
    }

    private function anmelden(): static
    {
        return $this->actingAs(new GenericUser(['id' => 7]));
    }

    /**
     * @param  array<string, mixed>  $attribute
     */
    private function kunde(array $attribute = []): Customer
    {
        return Customer::create($attribute + ['customer_type_id' => 2, 'first_name' => 'Max', 'last_name' => 'Muster', 'edit_user_id' => 1]);
    }

    // Charakterisierung: Gäste erhalten 401 (JSON) auf allen Kernrouten
    public function test_gaeste_erhalten_401(): void
    {
        $this->getJson('/customer/list')->assertUnauthorized();
        $this->getJson('/customer/create')->assertUnauthorized();
        $this->getJson('/customer/1')->assertUnauthorized();
        $this->postJson('/customer/store')->assertUnauthorized();
        $this->deleteJson('/customer/delete/1')->assertUnauthorized();
    }

    // Charakterisierung
    public function test_liste_rendert_view(): void
    {
        $this->anmelden()->get('/customer/list')->assertOk()->assertViewIs('customer::customer.index');
    }

    // Charakterisierung: "/customer" und "/customer/index" liefern dieselbe Liste
    public function test_wurzel_und_index_liefern_dieselbe_view(): void
    {
        $this->anmelden()->get('/customer')->assertOk()->assertViewIs('customer::customer.index');
        $this->anmelden()->get('/customer/index')->assertOk()->assertViewIs('customer::customer.index');
    }

    // Charakterisierung: ohne Kunden liefert die Ajax-Liste eine leere Datenmenge
    public function test_ajax_liste_ohne_kunden_ist_leer(): void
    {
        if (! class_exists(DataTables::class)) {
            $this->markTestSkipped('yajra/laravel-datatables-oracle nicht verfügbar');
        }

        $this->anmelden()->getJson('/customer/list', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    // OFFEN-7 (roter Test, Defekt): Die Ajax-Liste baut je Zeile die Aktionsbuttons mit route('projects.index');
    // diese Route gehoert zu einem anderen Paket und ist hier nicht definiert -> RouteNotFoundException (500),
    // sobald ein Kunde existiert.
    public function test_ajax_liste_mit_kunde_liefert_zeile(): void
    {
        if (! class_exists(DataTables::class)) {
            $this->markTestSkipped('yajra/laravel-datatables-oracle nicht verfügbar');
        }
        $this->kunde();

        $this->anmelden()->getJson('/customer/list', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('data.0.cname', 'Muster, Max');
    }

    // Charakterisierung: ohne veröffentlichte Config sind die Vorgabewerte leer
    public function test_create_rendert_formular_mit_leeren_vorgabewerten(): void
    {
        $antwort = $this->anmelden()->get('/customer/create')->assertOk()->assertViewIs('customer::customer.create');

        $this->assertNull($antwort->viewData('customer')->hourly_rate);
    }

    // Charakterisierung: mit Config werden Stundensatz und Kilometergeld vorbelegt
    public function test_create_belegt_werte_aus_der_config_vor(): void
    {
        config(['customer.hourly_rate' => '760', 'customer.mileage_allowance' => '1']);

        $antwort = $this->anmelden()->get('/customer/create')->assertOk();

        $this->assertEquals(760, $antwort->viewData('customer')->hourly_rate);
        $this->assertEquals(1, $antwort->viewData('customer')->mileage_allowance);
    }

    // Charakterisierung: Firma ohne Firmenname wird abgelehnt
    public function test_store_firma_ohne_firmenname_ist_ungueltig(): void
    {
        $this->anmelden()->post('/customer/store', [
            'customer_type_id' => 1, 'address_id' => 1, 'email' => 'a@b.de', 'invoice_type_id' => 1,
            'hourly_rate' => 10, 'mileage_allowance' => 1,
        ])->assertSessionHasErrors(['company_row_1']);

        $this->assertDatabaseCount('customers', 0);
    }

    // Charakterisierung: Privatkunde braucht Vor- und Nachname
    public function test_store_privatkunde_ohne_namen_ist_ungueltig(): void
    {
        $this->anmelden()->post('/customer/store', [
            'customer_type_id' => 2, 'address_id' => 1, 'email' => 'a@b.de', 'invoice_type_id' => 1,
            'hourly_rate' => 10, 'mileage_allowance' => 1,
        ])->assertSessionHasErrors(['first_name', 'last_name']);
    }

    // Charakterisierung
    public function test_store_prueft_email_und_zahlenfelder(): void
    {
        $this->anmelden()->post('/customer/store', [
            'customer_type_id' => 1, 'company_row_1' => 'ACME', 'address_id' => 1, 'email' => 'keine-mail',
            'invoice_type_id' => 1, 'hourly_rate' => 'abc', 'mileage_allowance' => 'x',
        ])->assertSessionHasErrors(['email', 'hourly_rate', 'mileage_allowance']);
    }

    // OFFEN-6 (roter Test, Defekt): store() ruft Customer::create($request->all()) ohne edit_user_id auf;
    // die Spalte ist NOT NULL ohne Default -> SQL-Fehler (500). Ausserdem ist edit_user_id fillable
    // und liesse sich per Request setzen (Mass-Assignment).
    public function test_store_legt_kunden_an_und_leitet_auf_die_liste(): void
    {
        $this->anmelden()->post('/customer/store', [
            'customer_type_id' => 1, 'company_row_1' => 'ACME', 'address_id' => 1, 'email' => 'a@b.de',
            'invoice_type_id' => 1, 'hourly_rate' => 10, 'mileage_allowance' => 1,
        ])->assertRedirect(route('customer.list'))->assertSessionHas('alert-type', 'success');

        $this->assertDatabaseHas('customers', ['company_row_1' => 'ACME', 'edit_user_id' => 7]);
    }

    // Charakterisierung
    public function test_edit_zeigt_vorhandenen_kunden(): void
    {
        $kunde = $this->kunde();

        $this->anmelden()->get('/customer/edit/'.$kunde->id)->assertOk()->assertViewIs('customer::customer.edit');
    }

    // Charakterisierung
    public function test_edit_und_show_unbekannter_kunde_ist_404(): void
    {
        $this->anmelden()->get('/customer/edit/999')->assertNotFound();
        $this->anmelden()->get('/customer/999')->assertNotFound();
    }

    // Charakterisierung
    public function test_show_zeigt_kunden(): void
    {
        $kunde = $this->kunde();

        $this->anmelden()->get('/customer/'.$kunde->id)->assertOk()->assertViewIs('customer::customer.show')->assertSee('Muster');
    }

    // Charakterisierung: Update speichert und setzt den bearbeitenden Benutzer
    public function test_update_speichert_und_setzt_edit_user_id(): void
    {
        $kunde = $this->kunde();

        $this->anmelden()->post('/customer/update/'.$kunde->id, [
            'customer_type_id' => 2, 'first_name' => 'Maria', 'last_name' => 'Muster', 'address_id' => 2,
            'email' => 'm@b.de', 'invoice_type_id' => 1, 'hourly_rate' => 10, 'mileage_allowance' => 1,
        ])->assertRedirect(route('customer.list'))->assertSessionHas('alert-type', 'success');

        $this->assertDatabaseHas('customers', ['id' => $kunde->id, 'first_name' => 'Maria', 'edit_user_id' => 7]);
    }

    // Charakterisierung
    public function test_update_mit_ungueltigen_daten_aendert_nichts(): void
    {
        $kunde = $this->kunde();

        $this->anmelden()->post('/customer/update/'.$kunde->id, ['customer_type_id' => 2])->assertSessionHasErrors();

        $this->assertDatabaseHas('customers', ['id' => $kunde->id, 'first_name' => 'Max']);
    }

    // Charakterisierung
    public function test_delete_loescht_per_soft_delete_und_leitet_auf_die_liste(): void
    {
        $kunde = $this->kunde();

        $this->anmelden()->delete('/customer/delete/'.$kunde->id)
            ->assertRedirect(route('customer.list'))
            ->assertSessionHas('message', 'Customer deleted successfully.');

        $this->assertSoftDeleted('customers', ['id' => $kunde->id]);
    }

    // Charakterisierung
    public function test_delete_unbekannter_kunde_ist_404(): void
    {
        $this->anmelden()->delete('/customer/delete/999')->assertNotFound();
    }
}
