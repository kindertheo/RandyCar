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
                'city' => [],
                'trips' => []
            ])
            ]
        );

        
        $this->assertResponseIsSuccessful();
    }

}
