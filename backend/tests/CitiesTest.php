<?php

namespace App\Tests;

use App\Entity\Car;
use App\Entity\City;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Utils;


class CitiesTest extends ApiTestCase
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

    // get{id}
    public function testGetById(): void 
    { 
        $randomIdCar = Utils::getRandomIdByCollections(City::class, $this->entityManager);
        $this->assertIsNumeric($randomIdCar);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/cities/'.$randomIdCar);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    
    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/cities');

        $this->assertMatchesResourceCollectionJsonSchema(City::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


     // TEST POST
     public function testPostCity() 
     { 
        $req = static::createClient()->request('POST','http://localhost/api/cities', [
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                'postalCode' => "6666",
                'name'=> "testPostNameCityOfTheDeath",
            ])
            ]
        );        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
     }
 
     // TEST DELETE 
     public function testDeleteCity() 
     { 
        $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/cities/' . $randomIdCity);
        $this->assertResponseStatusCodeSame(204);
    }

 
     // TEST PUT 
     public function testPutCity() 
     { 
        $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);
 
         $req = static::createClient()->request('PUT', 'http://localhost/api/cities/' . $randomIdCity, [ 
             'headers' => [ 
             'Content-Type' => 'application/json',
             'accept' => 'application/json'
         ],
         'body' => json_encode([
            'postalCode' => "6666",
            'name'=> "testPUTPUTPUTPUTPUTPUTPUTNameCityOfTheDeath",
         ])
         ] );
 
         $this->assertResponseIsSuccessful();
     }
}