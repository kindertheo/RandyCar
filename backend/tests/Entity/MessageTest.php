<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Messages;
use DateTime;
use DateTimeImmutable;

class MessageTest extends ApiTestCase
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

    // GET + testformat
    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/messages');
        $this->assertMatchesResourceCollectionJsonSchema(Messages::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //GET{id} 
    public function testGetById():void 
    { 
        // findAll
        $id = $this->entityManager->getRepository(Messages::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/messages/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');        
    }

     // TEST POST
     public function testPostMessages() 
     { 
         $body = [
             "authorId" => [ "api/users/20"],
             "receiverId" => [ "api/users/11"],
             "createdAt" => "2022-08-18T15:31:31.461Z",
             "content" => "lorem ipsum dolor sit amet",
             "isRead" => "2022-08-18T15:31:31.461Z"
         ];

         $req = static::createClient()->request('POST','http://localhost/api/messages', [
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