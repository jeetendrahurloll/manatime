<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use PDO;

class SpamCheckerTest extends TestCase
{


    /**
     *@beforeClass
     */
    public static function beforeClassFunction(): void
    {
        echo ("\n before class function \n");
    }


    /**
     *@afterClass
     */
    public static function afterClassFunction(): void
    {
        echo ("\n after class function \n");
    }

    /**
     * @before
     */
    public function someBefore(): void
    {
        echo ("\n somebefore \n");
    }


    /**
     * @after
     */
    public function someAfter(): void
    {
        echo ("\n someAfter \n");
    }



    /**
     *@after
     */
    public function anotherAfter(): void
    {
        echo ("\n abotherAfter \n");
    }






    public function testSomething(): void
    {
        //$dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
        $DATABASE_URL="mysql://root:mypswd@172.17.0.3:3306/app?serverVersion=8&charset=utf8mb4";
        $dbh = new PDO('mysql:host=172.17.0.3;dbname=app', 'root', 'mypswd');
        $parts = parse_url($DATABASE_URL);
        print_r($parts);

        echo("db url   ".$_ENV["DATABASE_URL"]);

        // use the connection here
        //$sth = $dbh->query("INSERT INTO manatime_equipment (name,category,number,description,created_at,updated_at)VALUES ('keyboard','input device','hyyhhyh656565','keyboard given to sanjeev','2023-06-13 12:23:45','2023-06-13 13:23:45')");
        $sth=$dbh->query("DELETE FROM manatime_equipment");

        // fetch all rows into array, by default PDO::FETCH_BOTH is used
        $rows = $sth->fetchAll();
    }

    public function testAPI(): void
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
