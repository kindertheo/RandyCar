<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Opinion;
use App\Entity\User;

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
        $userCollection = $this->entityManager->getRepository(User::class)->findAll();
        $userRandom = $userCollection[0]->getId();
        $userRandom2 = $userCollection[2]->getId();  

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

    // post 
    public function postOpinion() { 

        $randomEmitter = $this->entityManager->getRepository(User::class)->findAll();
        $randomEmitter = $randomEmitter[random_int(0, count($randomEmitter)-1)];
        $randomReceptor = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceptor = $randomReceptor[random_int(0, count($randomReceptor)-1)];

        $body = [ 
            "notation"=> 2,
            "message"=> "hello world",
            "emitter"=> $randomEmitter->getId(),
            "receptor"=> $randomReceptor->getId(),
            "createdAt"=> "2022-08-16T08:22:46.806Z"
        ];

        $req = static::createClient()->request('POST','http://localhost/api/users', [
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode($body)
            ]
        );   

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }
}