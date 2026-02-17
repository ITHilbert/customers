# Architekturübersicht: ITHilbert Customer

## Aufbau
MVC-Struktur für Kundenverwaltung.

## Wichtige Klassen
- `ITHilbert\Customer\CustomerServiceProvider`
- `ITHilbert\Customer\Entities\Customer` (Model)
- `ITHilbert\Customer\Http\Controllers\CustomerController`

## Datenmodell
- `customers`: Haupttabelle für Kunden.
- `name_address`: Addressdaten.
