<?php

namespace App\Tests;
use App\Entity\Mail;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class MailTest extends ApiTestCase
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

    public function testCountAndSuccess(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/mails');

        $queryResult = $this->entityManager
            ->getRepository(Mail::class)
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
        $response = static::createClient()->request('GET', 'http://localhost/api/mails');

        $this->assertMatchesResourceCollectionJsonSchema(Mail::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //get{id}
    public function testGetById()
    {
        $id = $this->entityManager->getRepository(Mail::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/mails/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

}