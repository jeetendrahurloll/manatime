<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;


class EquipmentAddTest extends TestCase
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





    //test the addition of an equipment to database
    public function testEquipmentAdd(): void
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

        $outputData = json_decode($response->getBody());


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



    //test the addition of an equipment to database with input json missing fields.
    public function testMalformedEquipmentAdd(): void
    {

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );
            unset($postData[array_keys($postData)[$i]]);
            $response = $client->post('/equipment/add', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }



    //test the addition of an equipment to database with input json having bad field keys.
    public function testWrongKeysInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        for ($i = 0; $i < 6; $i++) {
            $postData = array(
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );

            //add any string to the end to corrupt the key, "yay" in this test for example.
            $newKey = array_keys($postData)[$i] . "yay";
            $postData[$newKey] = $postData[array_keys($postData)[$i]];
            unset($postData[array_keys($postData)[$i]]);


            $response = $client->post('/equipment/add', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }

    //test that addition of an equipment is refused when name,number,createdAt are given empty
    public function testEmptyValuesInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );



            $postData[$value] = "";

            $response = $client->post('/equipment/add', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }


    //test that addition of an equipment is refused when name,number,createdAt are given NULL
    public function testNullValuesInJsonEquipmentAdd(): void
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);


        foreach (['name', 'number', 'createdAt'] as $value) {

            $postData = array(
                "name" => "someName",
                "category" => "someCategory",
                "number" => "someNumber",
                "description" => "someDescription",
                "createdAt" => "2023-06-14 21:30:02",
                "updatedAt" => "2023-06-14 21:30:02"
            );



            $postData[$value] = null;

            $response = $client->post('/equipment/add', [
                'body' => json_encode($postData)
            ]);
            $res_array = (array)json_decode($response->getBody());
            $this->assertArrayHasKey("message", $res_array);
            $this->assertEquals($res_array['message'], 'An error occurred.Some values might be blank or not according to requirements');
        }
    }
}
