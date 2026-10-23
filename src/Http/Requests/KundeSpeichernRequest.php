<?php

declare(strict_types=1);

namespace ITHilbert\Customer\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validierung beim Anlegen und Ändern eines Kunden (store und update teilen dieselben Regeln).
 *
 * edit_user_id ist bewusst nicht enthalten: Der Wert wird serverseitig aus dem angemeldeten Benutzer gesetzt
 * und darf über validated() nie aus dem Request kommen.
 */
final class KundeSpeichernRequest extends FormRequest
{
    /** Kundenart „Firma“ (customer_types.id = 1); sonst Privatkunde. */
    private const KUNDENART_FIRMA = 1;

    public function authorize(): bool
    {
        // Zugriff regelt die Middleware "auth" der Paket-Routen; kundenbezogene Rechte gibt es nicht.
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $firma = self::KUNDENART_FIRMA;

        return [
            'customer_type_id' => ['required', 'integer'],
            'company_row_1' => ["required_if:customer_type_id,{$firma}", 'nullable', 'string', 'max:255'],
            'company_row_2' => ['nullable', 'string', 'max:255'],
            'address_id' => ['required', 'integer'],
            'titel' => ['nullable', 'string', 'max:255'],
            'first_name' => ["required_unless:customer_type_id,{$firma}", 'nullable', 'string', 'max:255'],
            'last_name' => ["required_unless:customer_type_id,{$firma}", 'nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'ustid' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'fax' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'web' => ['nullable', 'string', 'max:255'],
            'invoice_type_id' => ['required', 'integer'],
            'hourly_rate' => ['required', 'numeric'],
            'mileage_allowance' => ['required', 'numeric'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
