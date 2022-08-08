<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Address;
use App\Entity\City;
use App\Entity\Trip;
use App\Repository\AddressRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\VarDumper\VarDumper;

class AddressTest extends ApiTestCase
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


    public function testCountAndSuccess(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses');

        $queryResult = $this->entityManager
            ->getRepository(Address::class)
            ->count([]);
        
        $content = $response->getContent();

        $count = json_decode($content, true); 
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses');

        $this->assertMatchesResourceCollectionJsonSchema(Address::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST GET{ID}
    public function testgetById(): void 
    { 
        // findAll
        $id = $this->entityManager->getRepository(Address::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST POST
    public function testPostAddress() 
    { 
        $allIdCities = $this->entityManager->getRepository(Address::class)->findAll();
        $allIdCities = count($allIdCities);
        $randomIdCities = random_int(1, 10);
        $req = static::createClient()->request('POST','http://localhost/api/addresses', [
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                'number' => "6",
                'street'=> "Rue de John Doe",
                'city' => "api/cities/". $randomIdCities,
                'trips' => []
            ])
            ]
        );        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    // TEST DELETE 
    public function testDeleteAddress() 
    { 
        $allId = $this->entityManager->getRepository(Address::class)->findAll();
        $randomAddress = $allId[random_int(0, count($allId) -1 )];
        $req = static::createClient()->request('DELETE', 'http://localhost/api/addresses/' . $randomAddress->getId());
        
        $countStart = count($randomAddress->getStartTrips());
        $countEnd = count($randomAddress->getEndTrips());

        //var_dump($count);
        if( $countStart == 0 && $countEnd == 0 ){
            $this->assertResponseStatusCodeSame(204);
        } else {
            $this->assertResponseStatusCodeSame(500);
        }
    }

    // TEST PUT 
    public function testPutAddress() 
    { 
        $allId = $this->entityManager->getRepository(Address::class)->findAll();
        $randomAddress = $allId[random_int(0, count($allId) -1 )];

        $allIdCities = $this->entityManager->getRepository(City::class)->findAll();
        $randomCity = $allIdCities[random_int(0, count($allIdCities) - 1 )];

        $allTrips = $this->entityManager->getRepository(Trip::class)->findAll();
        $randomTrip = $allTrips[random_int(0, count($allTrips) -1)]; 
        

        $req = static::createClient()->request('PUT', 'http://localhost/api/addresses/' . $randomAddress->getId(), [ 
            'headers' => [ 
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ],
        'body' => json_encode([
            'number' => "6",
            'street'=> "Rue de John Doe",
            'city' => "api/cities/" .$randomCity->getId(),
            'trips' => ["api/trips/" .$randomTrip->getId()]
        ])
        ] );

        $this->assertResponseIsSuccessful();
    }

}
