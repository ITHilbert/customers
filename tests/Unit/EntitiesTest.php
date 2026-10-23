<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests\Unit;

use ITHilbert\Customer\Database\Seeders\CustomerDatabaseSeeder;
use ITHilbert\Customer\Entities\Customer;
use ITHilbert\Customer\Entities\CustomerType;
use ITHilbert\Customer\Entities\InvoiceType;
use ITHilbert\Customer\Entities\NameAddress;
use ITHilbert\Customer\Tests\Concerns\MitLaravelKitAlias;
use ITHilbert\Customer\Tests\TestCase;

/**
 * Charakterisierung der Entities (Ist-Stand, keine Spec).
 * Läuft mit dem Behelf-Alias für OFFEN-1 (siehe MitLaravelKitAlias und BootTest).
 */
final class EntitiesTest extends TestCase
{
    use MitLaravelKitAlias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setzeLaravelKitAlias();
        $this->migrieren();
        $this->artisan('db:seed', ['--class' => CustomerDatabaseSeeder::class])->assertSuccessful();
    }

    /**
     * @param  array<string, mixed>  $attribute
     */
    private function kunde(array $attribute = []): Customer
    {
        return Customer::create($attribute + ['customer_type_id' => 2, 'first_name' => 'Max', 'last_name' => 'Muster', 'edit_user_id' => 1]);
    }

    // Charakterisierung
    public function test_name_einer_firma_ist_company_row_1(): void
    {
        $kunde = $this->kunde(['customer_type_id' => 1, 'company_row_1' => 'ACME GmbH']);

        $this->assertSame('ACME GmbH', $kunde->getName());
    }

    // Charakterisierung
    public function test_name_einer_privatperson_ist_nachname_komma_vorname(): void
    {
        $this->assertSame('Muster, Max', $this->kunde()->getName());
    }

    // Charakterisierung
    public function test_name_und_full_name_attribut_sind_vorname_nachname(): void
    {
        $kunde = $this->kunde();

        $this->assertSame('Max Muster', $kunde->full_name);
        $this->assertSame('Max Muster', $kunde->name);
    }

    // Charakterisierung: Anrede-Attribut aus address_id
    public function test_anrede_attribut_mappt_herr_frau_und_sonst_die_id(): void
    {
        $this->assertSame('Herr', $this->kunde(['address_id' => 1])->name_address);
        $this->assertSame('Frau', $this->kunde(['address_id' => 2])->name_address);
        $this->assertSame(3, $this->kunde(['address_id' => 3])->name_address);
    }

    // Charakterisierung
    public function test_anrede_text_wird_aus_tabelle_gelesen(): void
    {
        $this->assertSame('Frau', $this->kunde(['address_id' => 2])->getNameAddressText());
    }

    // Charakterisierung: Löschen ist ein Soft-Delete
    public function test_loeschen_ist_soft_delete(): void
    {
        $kunde = $this->kunde();
        $kunde->delete();

        $this->assertSoftDeleted('customers', ['id' => $kunde->id]);
        $this->assertCount(0, Customer::all());
    }

    // Charakterisierung: Combobox-Daten der Stammdaten als JSON mit cbKey/cbCaption
    public function test_combobox_daten_der_stammdaten(): void
    {
        $daten = json_decode(CustomerType::getComboBoxData(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(2, $daten);
        $this->assertSame(1, $daten[0]['cbKey']);
        $this->assertSame('Firma', $daten[0]['cbCaption']);
        $this->assertCount(3, json_decode(InvoiceType::getComboBoxData(), true, 512, JSON_THROW_ON_ERROR));
        $this->assertCount(2, json_decode(NameAddress::getComboBoxData(), true, 512, JSON_THROW_ON_ERROR));
    }

    // Charakterisierung: Combobox-Daten der Kunden zeigen den Anzeigenamen
    public function test_combobox_daten_der_kunden_nutzen_anzeigenamen(): void
    {
        $this->kunde();

        $daten = json_decode(Customer::getComboBoxData(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Muster, Max', $daten[0]['cbCaption']);
    }

    // OFFEN-4 (roter Test, Defekt): getCustomerTypeText() und getInvoiceTypeText() suchen mit address_id statt mit
    // customer_type_id bzw. invoice_type_id (Copy-Paste). Kunde Typ 2 (Privat), Anrede 1 -> liefert "Firma".
    public function test_kundenart_text_nutzt_customer_type_id(): void
    {
        $kunde = $this->kunde(['customer_type_id' => 2, 'address_id' => 1, 'invoice_type_id' => 3]);

        $this->assertSame('Privat', $kunde->getCustomerTypeText());
        $this->assertSame('Nicht EU Ausland', $kunde->getInvoiceTypeText());
    }

    // OFFEN-5 (roter Test, Defekt): Die Relation invoiceType() nutzt den Fremdschluessel 'invoice_typ_id'
    // (Tippfehler), die Spalte heisst invoice_type_id. Es wird immer das leere Default-Modell geliefert.
    public function test_relation_invoice_type_liefert_den_rechnungstyp(): void
    {
        $kunde = $this->kunde(['invoice_type_id' => 2]);

        $this->assertSame('EU- Reverse Charge', $kunde->invoiceType->invoice_type);
    }

    // Charakterisierung: Stammdaten-Entities ohne Zeitstempel
    public function test_stammdaten_entities_haben_keine_zeitstempel(): void
    {
        $this->assertFalse((new CustomerType)->usesTimestamps());
        $this->assertFalse((new InvoiceType)->usesTimestamps());
        $this->assertFalse((new NameAddress)->usesTimestamps());
        $this->assertTrue((new Customer)->usesTimestamps());
    }
}
