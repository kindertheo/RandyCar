<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Address;
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
        $req = static::createClient()->request('POST','http://localhost/api/addresses', [
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                'number' => "6",
                'street'=> "Rue de John Doe",
                'city' => "api/cities/14",
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
        $allId = count($allId);
        $randomId = random_int(1, $allId);
        $req = static::createClient()->request('DELETE', 'http://localhost/api/addresses/' . $randomId);
        $this->assertResponseIsSuccessful();
    }

    // TEST PUT 
    public function testPutAddress() 
    { 
        $allId = $this->entityManager->getRepository(Address::class)->findAll();
        $allId = count($allId);
        $randomId = random_int(1, $allId);
        $req = static::createClient()->request('PUT', 'http://localhost/api/addresses/' . $randomId, [ 
            'headers' => [ 
            'Content-Type' => 'application/json',
            'accept' => 'application/json'
        ],
        'body' => json_encode([
            'number' => "6",
            'street'=> "Rue de John Doe",
            'city' => "api/cities/14",
            'trips' => []
        ])
        ] );

        $this->assertResponseIsSuccessful();
    }

}
