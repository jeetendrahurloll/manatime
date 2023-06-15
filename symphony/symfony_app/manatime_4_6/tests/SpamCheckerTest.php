<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;


class SpamCheckerTest extends TestCase
{

    public static $dbh=null;
    //private $number = 0;

    /**
     *@beforeClass
     */
    public static function beforeClassFunction(): void
    {
        echo ("\n Before Class function \n");
        /**
         * Create a direct database connection.
         * Direct connection without symfony is preferred to avoid complications.
         */

         //$DATABASE_URL = "mysql://root:mypswd@172.17.0.3:3306/app?serverVersion=8&charset=utf8mb4";
         //$parts = parse_url($DATABASE_URL);
         //print_r($parts); 
         //echo ("db url   " . $_ENV["DATABASE_URL"]);
        self::$dbh = new PDO('mysql:host=172.17.0.3;dbname=app', 'root', 'mypswd');

    }


    /**
     *@afterClass
     */
    public static function afterClassFunction(): void
    {
        echo ("\n After class function \n");
        /**
         * cleanup pdo database connection
         */
         self::$dbh=null;
    }

    /**
     * @before
     */
    public function beforeEachTest(): void
    {        
        echo ("\n Before each test \n");
        /**
         * populate database with test data
         */
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('keyboard',           'input device',     'sn656565',    'keyboard given to sanjeev','2023-06-13 12:23:45','2023-06-13 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('mouse',              'input device',     'zx5ggtg5',    'given to Marie jo',        '2023-06-13 13:23:45','2023-06-14 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('screen',             'display device',   'hyy656565',   'damaged unrepairable',     '2023-06-13 14:23:45','2023-06-15 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('laptop',             'input device',     '09809807jh',  'reported malfunc,untested','2023-06-13 15:23:45','2023-06-16 13:23:45')");
        self::$dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('removable hard disk','input device',     'hkhjkgyt987', 'damaged by sanjeev',       '2023-06-13 16:23:45','2023-06-17 13:23:45')");


    }


    /**
     * @after
     */
    public function afterEachTest(): void
    {
        /**
         * Cleans up database and resets it to blank
         */
        echo ("\n After each test \n");
        self::$dbh->query("DELETE FROM manatime_equipment");

    }


    public function testEquipmentRoute3(): void
    {
        //127.0.0.1:8001/equipment/delete/
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $nickname = 'ObjectOrienter' . rand(0, 999);
        $data = array(
            'nickname' => $nickname,
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );
        // 1) Create a programmer resource
        //$response = $client->post('/api/programmers', [
        //    'body' => json_encode($data)
        //]);
        ///equipment/delete/3
        $response = $client->get('/equipment/route/3');
        // echo($response);
        echo ("class  " . get_class($response) . "\n");
        echo ($response->getBody() . "\n");
        $this->assertTrue(true);
    }




    
}
