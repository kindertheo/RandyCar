<?php

namespace App\Tests;

use App\Entity\Car;
use App\Entity\User;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncode;

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

    //GET
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

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetById()
    {
        $id = $this->entityManager->getRepository(Car::class);
        $selected = $id[0];
        $this->assertIsObject($id);
        $index = $id->getId();
        $this->assertIsNumeric($index);

        $response = static::createClient()->request('GET', 'http://localhost/api/cars'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json', 'charset=utf-8');
    }

    //POST 
    public function testPostCar() 
    { 
        
        $user = $this->entityManager->getRepository(User::class)->findAll();
        $user = $user[random_int(0, count($user) - 1)];

        $body = [ 
            "brand" => "testBrand",
            "model" => "modelTest",
            "color" => "colorTest",
            "seatNumber" => random_int(0, 5),
            "licensePlate" => "TEST1234",
            'ownerId' => $user->getId()
        ];

        $req = static::createClient()->request('POST', 'http://localhost/api/cars', [ 
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode($body)
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }
}