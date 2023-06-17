<?php

namespace App\Repository;

use App\Entity\ManatimeEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\WhereClauseBuilder;


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
    public WhereClauseBuilder $whereClauseBuilder;

    public function __construct(ManagerRegistry $registry,WhereClauseBuilder $whereClauseBuilder)
    {
        parent::__construct($registry, ManatimeEquipment::class);
        $this->whereClauseBuilder=$whereClauseBuilder;
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

     


    /**
     * Returns 1 entity using that id
     */

    public function findOneBySomeField($value): ?ManatimeEquipment
    {

        echo ("repository id " . $value);
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }




    /**
     * Find records as per $parametersAsArray
     * 
     */
    public function findByMultipleFields($parametersAsArray): array
    {
        
        $whereClause=$this->whereClauseBuilder->buildWhereClause($parametersAsArray);

        $sql = "SELECT * FROM manatime_equipment" . $whereClause;
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery()->fetchAllAssociative();

        return [
            "result" => $resultSet
        ];
    }



    
}
