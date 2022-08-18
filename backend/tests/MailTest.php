<?php

namespace App\Tests;
use App\Entity\Mail;
use App\Entity\User;
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

    public function getMail() 
    {
        $req = static::createClient()->request('GET', 'http://localhost/api/mails');
        $queryResult = $this->entityManager 
            ->getRepository(Mail::class)
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

    //post 
    public function postMail()
    {

        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[random_int(0, count($randomReceiver)-1 )];

        $body = [ 
            "receiver"=> $randomReceiver->getId(),
            "object"=> "Lorem Ipsum Dolor sit amet",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z"
        ];

        $req = static::createClient()->request('POST', 'http://localhost/api/mails/', [ 
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

    public function putMail()
    { 
        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[0];

        $body = [ 
            "receiver"=> $randomReceiver->getId(),
            "object"=> "Put edition",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z"
        ];
    }

    public function testDeleteMail() 
    { 
        $allId = $this->entityManager->getRepository(Mail::class)->findAll();
        $randomOpinion = $allId[random_int(0, count($allId) -1 )];
        $req = static::createClient()->request('DELETE', 'http://localhost/api/mails/' . $randomOpinion->getId());
        $this->assertResponseIsSuccessful();
    }

}