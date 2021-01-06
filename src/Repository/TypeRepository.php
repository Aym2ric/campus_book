<?php

namespace App\Repository;

use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Type|null find($id, $lockMode = null, $lockVersion = null)
 * @method Type|null findOneBy(array $criteria, array $orderBy = null)
 * @method Type[]    findAll()
 * @method Type[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    public function search($formData = [])
    {
        $qb = $this->createQueryBuilder('t');

        if (isset($formData['nom']) && $formData['nom'] != null) {
            $qb
                ->andWhere('t.nom LIKE :nom')
                ->setParameter('nom', '%' . $formData['nom'] . '%');
        }

        return $qb;
    }

    /*
    SELECT type.id, type.nom, COUNT(livre.id)
    FROM type
    LEFT JOIN livre ON livre.type_id = type.id
    GROUP BY type.id
    */
    public function searchTypeNbBook($formData = [])
    {
        $qb = $this->createQueryBuilder('t')
        ->select('t.id, t.image, t.nom, COUNT(l.id) as nbLivres')
        ->leftJoin('t.livres', 'l');

        if (isset($formData['nom']) && $formData['nom'] != null) {
            $qb
                ->andWhere('t.nom LIKE :nom')
                ->setParameter('nom', '%' . $formData['nom'] . '%');
        }

        return $qb->groupBy('t.id');
    }

    // /**
    //  * @return Type[] Returns an array of Type objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Type
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
