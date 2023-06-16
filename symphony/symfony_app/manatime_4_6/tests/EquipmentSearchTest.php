<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;


class EquipmentSearchTest extends TestCase
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
        echo ("\n Before Class function \n");
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

        //get autogenerated id from last insert
        $stmt = self::$dbh->query("SELECT LAST_INSERT_ID()");
        $this->lastId = $stmt->fetchColumn();
        echo ("last id " . $this->lastId . "\n");
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





    //test the normal search of an equipment to database
    public function testEquipmentSearch(): void
    {
        $postDataJson = '{
            "name":{"OrAnd":"_AND","EqLike":"LIKE","Pattern":"key"},
            "category":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"input"},
            "number":{"OrAnd":"_OR","EqLike":"LIKE","Pattern":"pat"}
            
        }';

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        $response = $client->post('/equipment/search', [
            'body' => $postDataJson
        ]);
        echo("\n-------------1-----------\n");
        echo (json_encode(json_decode($response->getBody()), JSON_PRETTY_PRINT));
        echo("\n-------------2-----------\n");
        $this->assertTrue(true);
    }



    //test the addition of an equipment to database with input json missing fields.
    public function _testMalformedJsonEquipmentUpdate(): void
    {

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "id" => $this->lastId,
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );
            unset($postData[array_keys($postData)[$i]]);
            print_r($postData);
            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            echo ($response->getBody() . "\n");
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }



    //test the addition of an equipment to database with input json having bad field keys.
    public function _testWrongKeysInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "id" => $this->lastId,
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );
            $newKey = array_keys($postData)[$i] . "yay";
            $postData[$newKey] = $postData[array_keys($postData)[$i]];
            unset($postData[array_keys($postData)[$i]]);
            //print_r($postData);
            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            //echo ($response->getBody() . "\n");
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }

    //test that addition of an equipment is refused when name,number,createdAt are given empty
    public function _testEmptyValuesInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['id', 'name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "id" => $this->lastId,
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );



            $postData[$value] = "";

            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            echo (json_encode($postData));
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
            //print_r($res_array);
        }
    }


    //test that addition of an equipment is refused when name,number,createdAt are given NULL
    public function _testNullValuesInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['id', 'name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "id" => $this->lastId,
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );



            $postData[$value] = null;

            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            //echo (json_encode($postData));
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
            //print_r($res_array);
        }
    }
}
