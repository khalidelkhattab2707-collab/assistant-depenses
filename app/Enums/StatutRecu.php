<?php
namespace App\Enums;

enum StatutRecu: string {
    case EnAttente = 'en_attente';
    case Traite    = 'traite';
    case Echoue    = 'echoue';

    public function label(): string {
        return match($this) {
            self::EnAttente => 'En attente',
            self::Traite    => 'Traité',
            self::Echoue    => 'Échoué',
        };
    }

    public function color(): string {
        return match($this) {
            self::EnAttente => 'warning',
            self::Traite    => 'success',
            self::Echoue    => 'danger',
        };
    }

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}