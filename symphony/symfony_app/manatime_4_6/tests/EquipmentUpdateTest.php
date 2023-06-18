<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;


class EquipmentUpdateTest extends TestCase
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





    //test the normal update of an equipment to database
    public function testEquipmentUpdate(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        $postData = array(
            "id"=>$this->lastId,
            "name" => "someName",
            "category" => "someCategory",
            "number" => "someNumber",
            "description" => "someDescription",
            "createdAt" => "2023-06-14 21:30:02",
            "updatedAt" => "2023-06-14 21:30:02"
        );
        $response = $client->post('/equipment/update', [
            'body' => json_encode($postData)
        ]);
 


        /**
         * Confirm that 1 record was actually updated in the database, and that no errors occured during persisting.
         * Ex:database connection errors etc.  
         * Also the fields that were saved must be the same as the post data.      
         */
        $sql = "SELECT * FROM  manatime_equipment WHERE id='".$postData["id"]."' AND name='" . $postData["name"] . "' AND category='" . $postData["category"] . "' AND number='" . $postData["number"] . "' AND description='" . $postData["description"] . "' AND created_at='" . $postData["createdAt"] . "' AND updated_at='" . $postData["updatedAt"] . "'";

        $equipmentData = self::$dbh->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertIsInt(intval($this->lastId));
        $this->assertEquals(sizeof($equipmentData), 1);
        $this->assertEquals($equipmentData[0]["id"], $this->lastId);
        $this->assertEquals($equipmentData[0]["name"], $postData["name"]);
        $this->assertEquals($equipmentData[0]["category"], $postData["category"]);
        $this->assertEquals($equipmentData[0]["number"], $postData["number"]);
        $this->assertEquals($equipmentData[0]["description"], $postData["description"]);
        $this->assertEquals($equipmentData[0]["created_at"], $postData["createdAt"]);
        $this->assertEquals($equipmentData[0]["updated_at"], $postData["updatedAt"]);

        /**
         * Check that json response complies to a hypothetical format as supplied by system architect
         */
        $outputData = (array)json_decode($response->getBody());
        $this->assertEquals("message",array_keys($outputData)[0]);
        $this->assertEquals($outputData["message"],'Updated equipment of id '.$this->lastId);


    }



    //test the update of an equipment to database with input json missing fields.
    public function testMalformedJsonEquipmentUpdate(): void
    {

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "id"=>$this->lastId,
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );
            unset($postData[array_keys($postData)[$i]]);
            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }



    //test the update of an equipment to database with input json having bad field keys.
    public function testWrongKeysInJsonEquipmentUpdate(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "id"=>$this->lastId,
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
            $response = $client->post('/equipment/update', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }

    //test that update of an equipment is refused when name,number,createdAt are given empty
    public function testEmptyValuesInJsonEquipmentUpdate(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['id','name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "id"=>$this->lastId,
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
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }


    //test that update of an equipment is refused when name,number,createdAt are given NULL
    public function testNullValuesInJsonEquipmentUpdate(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['id','name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "id"=>$this->lastId,
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
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }
}
