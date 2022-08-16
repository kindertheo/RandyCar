<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Opinion;
use App\Entity\User;
use App\Entity\Trip;
use App\Repository\AddressRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\VarDumper\VarDumper;

class OpinionTest extends ApiTestCase
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
    public function testGetOpinions()
    {
        $req = static::createClient()->request('GET', 'http://localhost/api/opinions');
        $queryResult = $this->entityManager 
            ->getRepository(Opinion::class)
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
        $response = static::createClient()->request('GET', 'http://localhost/api/opinions');

        $this->assertMatchesResourceCollectionJsonSchema(Opinion::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //get{id}
    public function testGetById()
    {
        // findAll
        $id = $this->entityManager->getRepository(Opinion::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/opinions/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //Put 
    public function testPutOpinions() 
    {         

        $opinionCollection = $this->entityManager->getRepository(Opinion::class)->findAll();
        $opinionRandom = $opinionCollection[0]->getId();  

        $body = [ 
            "message" => "testPut"
        ];

        $req = static::createClient()->request('PUT', 'http://localhost/api/opinions/'. $opinionRandom, [ 
            'headers' => [ 
                'Content-Type' => 'application/ld+json',
                'accept' => 'application/json'
            ],
            'body' => json_encode($body)
        ]);

        $this->assertResponseIsSuccessful();
    }

    // TEST DELETE 
    public function testDeleteOpinions() 
    { 
        $allId = $this->entityManager->getRepository(Opinion::class)->findAll();
        $randomOpinion = $allId[random_int(0, count($allId) -1 )];
        $req = static::createClient()->request('DELETE', 'http://localhost/api/opinions/' . $randomOpinion->getId());
        $this->assertResponseIsSuccessful();

    }
}