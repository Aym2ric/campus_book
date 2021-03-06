<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function search($formData = [])
    {
        $qb = $this->createQueryBuilder('u');

        if (isset($formData['username']) && $formData['username'] != null) {
            $qb
                ->andWhere('u.username LIKE :username')
                ->setParameter('username', '%' . $formData['username'] . '%');
        }

        if (isset($formData['nom']) && $formData['nom'] != null) {
            $qb
                ->andWhere('u.nom LIKE :nom')
                ->setParameter('nom', '%' . $formData['nom'] . '%');
        }

        if (isset($formData['prenom']) && $formData['prenom'] != null) {
            $qb
                ->andWhere('u.prenom LIKE :prenom')
                ->setParameter('prenom', '%' . $formData['prenom'] . '%');
        }

        if (isset($formData['roles']) && $formData['roles'] != null) {
            $qb
                ->andWhere('u.roles = :roles')
                ->setParameter('roles', $formData['roles']);
        }

        if (isset($formData['enabled'])) {
            if ($formData['enabled'] == true) {
                $qb
                    ->andWhere('u.enabled = 1');
            } else {
                $qb
                    ->andWhere('u.enabled = 0');
            }
        }

        return $qb;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
