<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CarTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/cars');

        $this->assertResponseIsSuccessful();
        // $this->assertJsonContains(['@id' => '/']);
    }
}
