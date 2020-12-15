<?php

namespace App\Entity\Etat;

/**
 * Liste des états d'un livre
 *
 *
 */
final class LivreEtat
{
    const DISPONIBLE = 'disponible';
    const PRETE = 'indisponible';
    const STOCK = 'stock';

    public static function getEtats()
    {
        return [
            self::DISPONIBLE,
            self::PRETE,
            self::STOCK
        ];
    }

    public static function getEtatsForSelect()
    {
        return [
            self::DISPONIBLE => 'disponible',
            self::PRETE => 'indisponible',
            self::STOCK => 'stock',
        ];
    }

}
