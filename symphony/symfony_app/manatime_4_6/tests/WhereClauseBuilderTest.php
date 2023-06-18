<?php

namespace App\Tests;

use App\Service\ValidateService;
use App\Service\WhereClauseBuilder;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class WhereClauseBuilderTest extends KernelTestCase
{
    /*
     *database pdo connection 
     */
    public static $dbh = null;

    /**
     * Last autogenerated id from test inserts to have an id to make test updates using that id
     */
    public $lastId = null;

    /**
     *@beforeClass
     */
    public static function beforeClassFunction(): void
    {
        /**
         * Create a direct database connection.
         * Direct connection without symfony is preferred to avoid complications.
         */
        self::$dbh = new PDO('mysql:host=172.17.0.3;dbname=app', 'root', 'mypswd');
    }


    /**
     *@afterClass
     */
    public static function afterClassFunction(): void
    {
        /**
         * cleanup pdo database connection
         */
        self::$dbh = null;
    }

    /**
     * @before
     */
    public function beforeEachTest(): void
    {
        /**clean and 
         * populate database with test data
         */
        self::$dbh->query("TRUNCATE TABLE manatime_equipment");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('keyboard',           'input device',     'sn656565',    'keyboard given to sanjeev','2023-06-13 12:23:45','2023-06-13 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('mouse',              'input device',     'zx5ggtg5',    'given to Marie jo',        '2023-06-13 13:23:45','2023-06-14 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('screen',             'display device',   'hyy656565',   'damaged unrepairable',     '2023-06-13 14:23:45','2023-06-15 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('laptop',             'input device',     '09809807jh',  'reported malfunc,untested','2023-06-13 15:23:45','2023-06-16 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('removable hard disk','input device',     'hkhjkgyt987', 'damaged by sanjeev',       '2023-06-13 16:23:45','2023-06-17 13:23:45')");

        //get autogenerated id from last insert
        $stmt = self::$dbh->query("SELECT LAST_INSERT_ID()");
        $this->lastId = $stmt->fetchColumn();
    }


    /**
     * @after
     */
    public function afterEachTest(): void
    {
        /**
         * Cleans up database and resets it to blank
         */
        self::$dbh->query("DELETE FROM manatime_equipment");
    }



    /**
     * Test the WhereClauseBuilder which constructs a partial SQL query from json parameters
     */
 

    /**
     * @dataProvider  Provider
     */
    public function testObtainPartialSQL($postDataJson, $expectedResp): void
    {
        
        //get validateService instance from dependency container
        self::bootKernel();
        $container = static::getContainer();
        $whereClauseBuilder= $container->get(WhereClauseBuilder::class);

        //convert input json to params array
        $parametersAsArray = json_decode($postDataJson, true);

        $actualResponse=$whereClauseBuilder->buildWhereClause($parametersAsArray);
        
        //try to make the strings more comparable by removing white characters and reducing probability of unepredictible differences.
        //$expectedResp = '{"result":[{"id":1,"name":"keyboard","category":"input device","number":"sn656565","description":"keyboard given to sanjeev","created_at":"2023-06-13 12:23:45","updated_at":"2023-06-13 13:23:45"},{"id":2,"name":"mouse","category":"input device","number":"zx5ggtg5","description":"given to Marie jo","created_at":"2023-06-13 13:23:45","updated_at":"2023-06-14 13:23:45"},{"id":4,"name":"laptop","category":"input device","number":"09809807jh","description":"reported malfunc,untested","created_at":"2023-06-13 15:23:45","updated_at":"2023-06-16 13:23:45"},{"id":5,"name":"removable hard disk","category":"input device","number":"hkhjkgyt987","description":"damaged by sanjeev","created_at":"2023-06-13 16:23:45","updated_at":"2023-06-17 13:23:45"}]}';
        $expectedResp = str_replace(" ", "", $expectedResp);
        $actualResponse = str_replace(" ", "", $actualResponse);


        //compute Levenshtein difference between 2 strings
        $lev = levenshtein($expectedResp, $actualResponse);

        //assert that there are less than 5 chars of difference between expected and actual response
        $this->assertLessThan(5, $lev);
    }


     //data provider    
     public function Provider()
     {
         return array(
             [
                 '{
                     "name":{"OrAnd":"_AND","EqLike":"LIKE","Pattern":"key"},
                     "category":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"input"},
                     "number":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"656"}                
                 }',
                 "WHERE  name  LIKE  '%key%' OR  category  LIKE  '%input%' OR  number  LIKE  '%656%'"
             ],
             [
                 '{
                     "number":{"OrAnd":"_AND","EqLike":"EQUAL","Pattern":"sn656565"},
                     "id":{"OrAnd":"_OR","EqLike":"EQUAL","Pattern":"1"},
                     "name":{"OrAnd":"_AND","EqLike":"LIKE","Pattern":"keyboard"},
                     "category":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"input"},
                     "description":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"given"},
                     "created_at":{"OrAnd":"_OR","Comparator":"greater","Date":"1995-08-05 16:18:30"},
                     "updated_at":{"OrAnd":"_OR","Comparator":"greater","Date":"1995-06-05 19:18:30"}
                 }',
                 "WHERE  id  =  '1' AND  name  LIKE  '%keyboard%' OR  category  LIKE  '%input%' AND  number  =  'sn656565' OR  description  LIKE  '%given%' OR  created_at  > '1995-08-05 16:18:30'  OR  updated_at  > '1995-06-05 19:18:30'"
 
             ],
             [
                 '{                
                 }',
                 ''
             ]
 
         );
     }
   
}
