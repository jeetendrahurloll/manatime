<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class SpamCheckerTest extends TestCase
{
    public function testSomething(): void
    {

        for ($x = 0; $x <= 10; $x++) {

            $this->assertTrue(true);

        }

    }

    public function testAPI():void
    {
        //127.0.0.1:8001/equipment/delete/
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);

        $nickname = 'ObjectOrienter'.rand(0, 999);
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
           echo("class  ".get_class($response));
           echo($response->getBody());
        $this->assertTrue(true);

    }
}
