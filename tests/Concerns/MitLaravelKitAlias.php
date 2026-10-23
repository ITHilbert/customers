<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Tests\Concerns;

use ITHilbert\LaravelKit\Entities\ComboBox;
use ITHilbert\LaravelKit\Traits\VueComboBox;

/**
 * Behelf für OFFEN-1: Die Entities des Pakets verwenden ITHilbert\Vue\...; die Klassen liegen heute in
 * ITHilbert\LaravelKit\.... Der Alias (prozessweit, nur in Tests) erlaubt es, das übrige Verhalten zu prüfen.
 * Er ersetzt die Reparatur im Produktivcode nicht; der Test OFFEN-1 bleibt davon unberührt (statische Prüfung).
 */
trait MitLaravelKitAlias
{
    protected function setzeLaravelKitAlias(): void
    {
        if (! trait_exists(VueComboBox::class)) {
            $this->markTestSkipped('ithilbert/laravel-kit nicht verfügbar');
        }

        if (! trait_exists('ITHilbert\Vue\Traits\VueComboBox', false)) {
            class_alias(VueComboBox::class, 'ITHilbert\Vue\Traits\VueComboBox');
        }

        if (! class_exists('ITHilbert\Vue\Entities\ComboBox', false)) {
            class_alias(ComboBox::class, 'ITHilbert\Vue\Entities\ComboBox');
        }
    }
}
