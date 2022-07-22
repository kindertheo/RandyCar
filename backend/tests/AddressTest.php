<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\QueryBuilder;

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
    }

    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses');
        $this->assertMatchesResourceCollectionJsonSchema(Address::class);
    }
}
