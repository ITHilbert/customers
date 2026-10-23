<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests\Feature;

use ITHilbert\Customer\Tests\TestCase;

/**
 * Charakterisierung der Übersetzungen (Ist-Stand, keine Spec).
 */
final class UebersetzungTest extends TestCase
{
    // Charakterisierung: die Sprachdateien liegen im Paket in de/ und en/
    public function test_sprachdateien_im_paket_vorhanden(): void
    {
        $de = require __DIR__.'/../../src/Resources/lang/de/customer.php';
        $en = require __DIR__.'/../../src/Resources/lang/en/customer.php';

        $this->assertSame('Kundenliste', $de['header_list']);
        $this->assertSame('Name', $en['name'] ?? null);
    }

    // Regression: en/customer.php fehlen 7 Schluessel aus de/customer.php
    // (header_list, header_edit, header_release, customer, customers, MsgAddSuccess, MsgEditSuccess).
    public function test_englische_sprachdatei_enthaelt_alle_schluessel_der_deutschen(): void
    {
        $de = require __DIR__.'/../../src/Resources/lang/de/customer.php';
        $en = require __DIR__.'/../../src/Resources/lang/en/customer.php';

        $this->assertSame([], array_values(array_diff(array_keys($de), array_keys($en))));
    }

    // Regression: loadTranslationsFrom() zeigt auf resource_path('/Resources/lang/vendor/customer')
    // (Host-Pfad, existiert nicht) statt auf die Sprachdateien des Pakets. Der Schluessel wird daher nie uebersetzt.
    public function test_deutsche_uebersetzung_wird_geladen(): void
    {
        $this->app->setLocale('de');

        $this->assertSame('Kundenliste', trans('customer::customer.header_list'));
    }
}
