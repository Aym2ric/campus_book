<?php

namespace App\Repository;

use App\Entity\Etat\LivreEtat;
use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function searchDashboard($formData = [])
    {
        $qb = $this->createQueryBuilder('l')
        ->andWhere('l.etat = :etat')
        ->setParameter('etat', LivreEtat::DISPONIBLE);

        if (isset($formData['nom']) && $formData['nom'] != null) {
            $qb
                ->andWhere('l.nom LIKE :nom')
                ->setParameter('nom', '%' . $formData['nom'] . '%');
        }

        if (isset($formData['type']) && $formData['type'] != null) {
            $qb
                ->andWhere('l.type = :type')
                ->setParameter('type',  $formData['type']);
        }

        return $qb;
    }

    public function search($formData = [])
    {
        $qb = $this->createQueryBuilder('l');

        if (isset($formData['nom']) && $formData['nom'] != null) {
            $qb
                ->andWhere('l.nom LIKE :nom')
                ->setParameter('nom', '%' . $formData['nom'] . '%');
        }

        if (isset($formData['etat']) && $formData['etat'] != null) {
            $qb
                ->andWhere('l.etat = :etat')
                ->setParameter('etat', $formData['etat']);
        }

        if (isset($formData['bloquerProchaineReservation'])) {
            if ($formData['bloquerProchaineReservation'] == true) {
                $qb
                    ->andWhere('l.bloquerProchaineReservation = 1');
            } else {
                $qb
                    ->andWhere('l.bloquerProchaineReservation = 0');
            }
        }

        return $qb;
    }

    public function livresDisponibles()
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.etat = :etat')
            ->setParameter('etat', LivreEtat::DISPONIBLE);

        return $qb;
    }

    public function livresEmprunterDeUtilisateur(string $userId)
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.reserverPar = :reserverPar')
            ->setParameter('reserverPar', $userId);

        return $qb->getQuery()->getResult();
    }

    public function livresPreterDeUtilisateur(string $userId)
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('l.preterPar = :preterPar')
            ->setParameter('preterPar', $userId);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
