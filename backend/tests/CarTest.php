<?php

namespace App\Tests;

use App\Entity\Car;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CarTest extends ApiTestCase
{
    protected function setUp(): void 
    { 
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testGetCar()
    {
        $req = static::createClient()->request('GET', 'http://localhost/api/cars');
        $queryResult = $this->entityManager 
            ->getRepository(Car::class)
            ->count([]);

        $content = $req->getContent();

        $count = json_decode($content, true);
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);
    }
}