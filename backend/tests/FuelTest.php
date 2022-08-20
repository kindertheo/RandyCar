<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Fuel;
use App\Entity\Car;
use App\Entity\City;
use Doctrine\ORM\QueryBuilder;
use App\Tests\Utils;


class FuelTest extends ApiTestCase
{
    private $entityManager;

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
    public function testGetFuel()
    {
        $req = static::createClient()->request('GET', 'http://localhost/api/fuels');
        $queryResult = $this->entityManager 
            ->getRepository(Fuel::class)
            ->count([]);

        $content = $req->getContent();

        $count = json_decode($content, true);
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/fuels');

        $this->assertMatchesResourceCollectionJsonSchema(Fuel::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //get{id}
    public function testGetById()
    {
        // findAll
        $id = $this->entityManager->getRepository(Fuel::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/fuels/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


     // TEST POST
     public function testPostFuel() 
     { 
        $randomIdCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);

        // $allIdCar = $this->entityManager->getRepository(Car::class)->findAll();
        // $allIdCar = count($allIdCar);
        // $randomIdCar = random_int(1, 10);
        $req = static::createClient()->request('POST','http://localhost/api/fuels', [
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                'name' => "Test",
                'consumption'=> 1.56,
                'cars' => ["api/cars/". $randomIdCar],
            ])
            ]
        );        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
     }
 
     // TEST DELETE 
     public function testDeleteFuel() 
     { 
        $randomIdFuel = Utils::getRandomIdByCollections(Fuel::class, $this->entityManager);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/fuels/' . $randomIdFuel);
        $this->assertResponseStatusCodeSame(204);
    }

 
     // TEST PUT 
     public function testPutFuel() 
     { 
        $randomIdCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);
        $randomIdFuel = Utils::getRandomIdByCollections(Fuel::class, $this->entityManager);
 
         $req = static::createClient()->request('PUT', 'http://localhost/api/fuels/' . $randomIdFuel, [ 
             'headers' => [ 
             'Content-Type' => 'application/json',
             'accept' => 'application/json'
         ],
         'body' => json_encode([
            'name' => "TestPut",
            'consumption'=> 6.66,
            'cars' => ["api/cars/". $randomIdCar],
         ])
         ] );
 
         $this->assertResponseIsSuccessful();
     }
}