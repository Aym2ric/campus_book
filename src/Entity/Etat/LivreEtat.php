<?php

namespace App\Entity\Etat;

/**
 * Liste des états d'un livre
 *
 *
 */
final class LivreEtat
{
    const DISPONIBLE = 'Disponible'; // dispo à la réservation
    const INDISPONIBLE = 'Indisponible'; // réservé par quelqu'un
    const DEMAMDE_VALIDATION = 'Validation'; // demande de prêt, il faut valider le livre

    public static function getEtats()
    {
        return [
            self::DISPONIBLE,
            self::INDISPONIBLE,
            self::DEMAMDE_VALIDATION
        ];
    }

    public static function getEtatsForSelect()
    {
        return [
            self::DISPONIBLE => 'Disponible',
            self::INDISPONIBLE => 'Indisponible',
            self::DEMAMDE_VALIDATION => 'Validation'
        ];
    }

}
