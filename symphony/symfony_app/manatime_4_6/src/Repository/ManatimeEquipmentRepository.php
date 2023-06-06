<?php

namespace App\Repository;

use App\Entity\ManatimeEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ManatimeEquipment>
 *
 * @method ManatimeEquipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ManatimeEquipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ManatimeEquipment[]    findAll()
 * @method ManatimeEquipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManatimeEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManatimeEquipment::class);
    }

    public function save(ManatimeEquipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ManatimeEquipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ManatimeEquipment[] Returns an array of ManatimeEquipment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ManatimeEquipment
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByMultipleFields($parametersAsArray): array
    {
        try {
            echo "find by multiple fields";
            $id = 7;
            $qb = $this->createQueryBuilder('p');
            /*
                ->andWhere('p.id = :val')
                ->setParameter('val', $id);
            */

            /*
            $term = "number";
            $qb->andWhere('p.number LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $term . '%');
            */

            //dynamically Build query for id
            if ($parametersAsArray["id"]["OrAnd"] == "_OR") {
                if ($parametersAsArray["id"]["EqLike"] == "LIKE") {
                    $whereClause = 'p.id LIKE :id';
                    $parameter='%'.$parametersAsArray["id"]["Pattern"].'%';
                } else {
                    $whereClause = 'p.id = :id';
                    $parameter=$parametersAsArray["id"]["Pattern"];

                }
                $qb->orWhere($whereClause)
                    ->setParameter('id', $parameter);
            } else {
                if ($parametersAsArray["id"]["EqLike"] == "LIKE") {
                    $whereClause = 'p.id LIKE :id';
                    $parameter='%'.$parametersAsArray["id"]["Pattern"].'%';

                } else {
                    $whereClause = 'p.id = :id';
                    $parameter=$parametersAsArray["id"]["Pattern"];

                }
                $qb->andWhere($whereClause)
                    ->setParameter('id', $parameter);
            }

            //dynamically Build query for name







            $query = $qb->getQuery();
            //echo $query->getSQL(); 
            $result = $query->getArrayResult();
        } catch (\Throwable $e) {
            echo "exception !!!";
        }

        return $result;
    }
}
