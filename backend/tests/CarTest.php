<?php

namespace App\Tests;

use App\Entity\Car;
use App\Entity\User;
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


    //get{id}
    public function testGetById()
    {
        // findAll
        $id = $this->entityManager->getRepository(Car::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/cars/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //Put 
    public function testPutCar() 
    {         

        $carCollection = $this->entityManager->getRepository(Car::class)->findAll();
        $carRandom = $carCollection[0]->getId();  

        $body = [ 
            "brand" => "testBrand",
            "model" => "modelTest",
            "color" => "colorTest",
            "seatNumber" => random_int(0, count($carCollection)-1),
            "licensePlate" => "TEST1234"
        ];

        $req = static::createClient()->request('PUT', 'http://localhost/api/cars/'. $carRandom, [ 
            'headers' => [ 
                'Content-Type' => 'application/ld+json',
                'accept' => 'application/json'
            ],
            'body' => json_encode($body)
        ]);

        $this->assertResponseIsSuccessful();
    }
}