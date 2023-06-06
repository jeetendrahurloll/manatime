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

    public function findByMultipleFields_deprecated($parametersAsArray): array
    {
        try {
            //echo "find by multiple fields";
            $id = 1;
            $name = "name";
            $category = "peripheral";
            $qb = $this->createQueryBuilder('p')
                ->orWhere('p.id = :id')
                ->setParameter('id', $id)
                ->orWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%')
                ->orWhere('p.category LIKE :category')
                ->setParameter('category', '%' . $category . '%');

            /*
            $term = "number";
            $qb->andWhere('p.number LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $term . '%');
            */

            //dynamically Build query for id
            /*
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

            if ($parametersAsArray["name"]["OrAnd"] == "_OR") {
                if ($parametersAsArray["name"]["EqLike"] == "LIKE") {
                    $whereClause = 'p.name LIKE :name';
                    $parameter='%'.$parametersAsArray["name"]["Pattern"].'%';
                } else {
                    $whereClause = 'p.id = :name';
                    $parameter=$parametersAsArray["name"]["Pattern"];

                }
                $qb->orWhere($whereClause)
                    ->setParameter('name', $parameter);
            } else {
                if ($parametersAsArray["name"]["EqLike"] == "LIKE") {
                    $whereClause = 'p.name LIKE :name';
                    $parameter='%'.$parametersAsArray["name"]["Pattern"].'%';

                } else {
                    $whereClause = 'p.name = :name';
                    $parameter=$parametersAsArray["name"]["Pattern"];

                }
                $qb->andWhere($whereClause)
                    ->setParameter('name', $parameter);
            }

            */



            $query = $qb->getQuery();
            echo "{" . $query->getSQL() . "}";
            $result = $query->getArrayResult();
        } catch (\Throwable $e) {
            echo "exception !!!";
        }

        return $result;
    }

    public function findByMultipleFields($parametersAsArray): array
    {

        $conn = $this->getEntityManager()->getConnection();
        /*
        $orAnd=$parametersAsArray[$keys[0]]['OrAnd'];           //SQL OR or AAND
        $column=$keys[0];                                       //id,name,category....   
        $sqlOperator=$parametersAsArray[$keys[0]]['EqLike'];    //EQUAL or LIKE
        $pattern=$parametersAsArray[$keys[0]]['Pattern'];        //input pattern
        */
        $whereClause = '';                                       //a string that comprises all the SQL WHERE statements
        $sqlOperatorsMap = [
            '_OR' => 'OR',
            '_AND' => 'AND',
            'EQUAL' => '=',
            'LIKE' => 'LIKE',
            'equal' => '=',
            'greater' => '>',
            'less' => '<'
        ];


        echo "1";
        $whereClauseFragment1 = "";                                 //where clause for columns
        $first = true;
        foreach (['id', 'name', 'category', 'number', 'description'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }

            //$column = $keys[$x];                                   //id,name,category....   
            $orAnd = $parametersAsArray[$column]['OrAnd'];           //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['EqLike'];    //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $pattern = $parametersAsArray[$column]['Pattern'];        //input pattern
            $sqlWildcard = '%';

            if ($sqlOperator == 'EQUAL') {
                $sqlWildcard = '';
            } else {
                $sqlWildcard = '%';
            }

            //remove first OR or AND stament in where clause            
            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }

            $whereClauseFragment1 = $whereClauseFragment1 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . '  \'' . $sqlWildcard . $pattern . $sqlWildcard . '\'';
            //echo "-where->  " . $whereClauseFragment1 . "\n";
        }

        $whereClauseFragment2 = "";                                         //where clause for dates
        foreach (['created_at', 'updated_at'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }
            $orAnd = $parametersAsArray[$column]['OrAnd'];                  //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['Comparator'];       //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $date = $parametersAsArray[$column]['Date'];                    //input pattern
            $sqlWildcard = '%';

            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }
            $whereClauseFragment2 = $whereClauseFragment2 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . ' \'' . $date . '\' ';
        }

        $whereClause = $whereClauseFragment1 . $whereClauseFragment2; //concatenate both fragment of where clauses
        echo("funstion where clause:".$this->buildWhereClause($parametersAsArray));
        $sql = "SELECT * FROM manatime_equipment" . $whereClause;
        echo $sql;

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $result = ['SQL' => $sql, 'result' => $resultSet->fetchAllAssociative()];

        // returns an array of arrays (i.e. a raw data set) and the executed sql query
        return $result;
    }


    private function buildWhereClause($parametersAsArray): string
    {   /*
        $orAnd=$parametersAsArray[$keys[0]]['OrAnd'];           //SQL OR or AAND
        $column=$keys[0];                                       //id,name,category....   
        $sqlOperator=$parametersAsArray[$keys[0]]['EqLike'];    //EQUAL or LIKE
        $pattern=$parametersAsArray[$keys[0]]['Pattern'];        //input pattern
        */
        $whereClause = '';                                       //a string that comprises all the SQL WHERE statements
        $sqlOperatorsMap = [
            '_OR' => 'OR',
            '_AND' => 'AND',
            'EQUAL' => '=',
            'LIKE' => 'LIKE',
            'equal' => '=',
            'greater' => '>',
            'less' => '<'
        ];


        echo "1";
        $whereClauseFragment1 = "";                                 //where clause for columns
        $first = true;
        foreach (['id', 'name', 'category', 'number', 'description'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }

            //$column                                                //id,name,category....   
            $orAnd = $parametersAsArray[$column]['OrAnd'];           //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['EqLike'];    //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $pattern = $parametersAsArray[$column]['Pattern'];        //input pattern
            $sqlWildcard = '%';

            if ($sqlOperator == 'EQUAL') {
                $sqlWildcard = '';
            } else {
                $sqlWildcard = '%';
            }

            //remove first OR or AND stament in where clause            
            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }

            $whereClauseFragment1 = $whereClauseFragment1 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . '  \'' . $sqlWildcard . $pattern . $sqlWildcard . '\'';
        }

        $whereClauseFragment2 = "";                                         //where clause for dates
        foreach (['created_at', 'updated_at'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }
            $orAnd = $parametersAsArray[$column]['OrAnd'];                  //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['Comparator'];       //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $date = $parametersAsArray[$column]['Date'];                    //input pattern
            $sqlWildcard = '%';

            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }
            $whereClauseFragment2 = $whereClauseFragment2 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . ' \'' . $date . '\' ';
        }

        $whereClause = $whereClauseFragment1 . $whereClauseFragment2; //concatenate both fragment of where clauses
        return $whereClause;
    }
}
