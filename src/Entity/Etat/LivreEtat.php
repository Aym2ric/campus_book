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

    public static function getEtats()
    {
        return [
            self::DISPONIBLE,
            self::INDISPONIBLE
        ];
    }

    public static function getEtatsForSelect()
    {
        return [
            self::DISPONIBLE => 'Disponible',
            self::INDISPONIBLE => 'Indisponible'
        ];
    }

}
