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

    public function findByMultipleFields(): array
    {
        try {
            echo "find by multiple fields";
            $id = 1;
            $qb = $this->createQueryBuilder('p');


            //if (!$includeUnavailableProducts) {
            //    $qb->andWhere('p.available = TRUE');
            //}

            $query = $qb->getQuery();
            
            //$result = $query->execute();
            $result = $query->getArrayResult();
        } catch (\Exception $e) {
            echo "exception !!!";
        }

        return $result;
    }
}
