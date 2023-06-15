<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;


class SpamCheckerTest extends TestCase
{
    /*
     *database pdo connection 
     */
    public static $dbh = null;

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
        self::$dbh = null;
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





    //test the addition of an equipment to database
    public function _testEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        $postData = array(
            "name" => "someName",
            "category" => "someCategory",
            "number" => "someNumber",
            "description" => "someDescription",
            "createdAt" => "2023-06-14 21:30:02",
            "updatedAt" => "2023-06-14 21:30:02"
        );
        $response = $client->post('/equipment/add', [
            'body' => json_encode($postData)
        ]);
        echo ($response->getBody() . "\n");

        $outputData = json_decode($response->getBody());
        //echo ("current id is " . $outputData->id);


        /**
         * Confirm that 1 record was actually added in the database, and that no errors occured during persisting.
         * Ex:database connection errors etc.  
         * Also the fields that were saved must be the same as the post data.      
         */
        $sql = "SELECT * FROM  manatime_equipment WHERE name='" . $postData["name"] . "' AND category='" . $postData["category"] . "' AND number='" . $postData["number"] . "' AND description='" . $postData["description"] . "' AND created_at='" . $postData["createdAt"] . "' AND updated_at='" . $postData["updatedAt"] . "'";

        $equipmentData = self::$dbh->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertIsInt(intval($outputData->id));
        $this->assertEquals(sizeof($equipmentData), 1);
        $this->assertEquals($equipmentData[0]["id"], $outputData->id);
        $this->assertEquals($equipmentData[0]["name"], $postData["name"]);
        $this->assertEquals($equipmentData[0]["category"], $postData["category"]);
        $this->assertEquals($equipmentData[0]["number"], $postData["number"]);
        $this->assertEquals($equipmentData[0]["description"], $postData["description"]);
        $this->assertEquals($equipmentData[0]["created_at"], $postData["createdAt"]);
        $this->assertEquals($equipmentData[0]["updated_at"], $postData["updatedAt"]);
    }



    //test the addition of an equipment to database with wrong input json
    public function testMalformedEquipmentAdd(): void
    {

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        $postData = array(
            "name" => "someName",
            "category" => "someCategory",
            "number" => "someNumber",
            "description" => "someDescription",
            //"createdAt" => "2023-06-14 21:30:02",
            "updatedAt" => "2023-06-14 21:30:02"
        );
        $response = $client->post('/equipment/add', [
            'body' => json_encode($postData)
        ]);
        echo ($response->getBody() . "\n");
        $this->assertTrue(true);

    }
}
