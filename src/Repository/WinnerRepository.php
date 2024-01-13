<?php

namespace App\Repository;

use App\Entity\Winner;
use Doctrine\Bundle\DoctrineBundle\Repository\LazyServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends LazyServiceEntityRepository<Winner>
 *
 * @method Winner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Winner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Winner[]    findAll()
 * @method Winner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WinnerRepository extends LazyServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Winner::class);
    }

    /**
     * @param int $itemsPerPage
     * @param int $page
     *
     * @return float|int|mixed|string
     */
    public function getPaginated(int $itemsPerPage, int $page): mixed
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $query = $queryBuilder
            ->orderBy('p.id', 'ASC')
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return bool|float|int|string|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotal(): float|bool|int|string|null
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Winner $winner
     *
     * @return void
     */
    public function save(Winner $winner): void
    {
        $em = $this->getEntityManager();
        $em->persist($winner);
        $em->flush();
    }


    /**
     * @param $id
     *
     * @return string|int|float
     */
    public function remove($id): string|int|float
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->delete(Winner::class, 'c')
            ->where('c.id = :id')
            ->setParameter("id", $id)
            ->getQuery()
            ->execute();
    }
}
