<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text_brut' => ['required', 'string', 'min:10', 'max:10000'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,webp', 'max:10240'],
            'devis' => ['nullable', 'string', Rule::in(['MAD', 'EUR', 'USD'])],
            'total_estime' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'text_brut.required' => 'Le texte du reçu est obligatoire.',
            'text_brut.min' => 'Le texte du reçu doit contenir au moins 10 caractères.',
            'text_brut.max' => 'Le texte du reçu ne peut pas dépasser 10 000 caractères.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpg, png ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 10 Mo.',
            'devis.in' => 'La devise sélectionnée est invalide. Valeurs acceptées : MAD, EUR, USD.',
            'total_estime.min' => 'Le total estimé doit être un nombre positif.',
        ];
    }
}
