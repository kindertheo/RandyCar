<?php

namespace App\Tests;

use App\Entity\User;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', 'http://localhost/api/users');

        $queryResult = $this->entityManager
            ->getRepository(User::class)
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
        $response = static::createClient()->request('GET', 'http://localhost/api/users');

        $this->assertMatchesResourceCollectionJsonSchema(User::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST GET{ID}
    public function testgetById(): void 
    { 
        // findAll
        $id = $this->entityManager->getRepository(User::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/users/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

     // TEST POST
     public function testPostUser() 
     { 
         $body = [
             "name"=> "Random",
             "surname" => "Dupont",
             "email" => "connard@gmail.com",
             "phone" => "3630",
             "password" => "tamere",
             "avatar" => "des putes",
             "bio" => "biographie",
             "cars" => [ "api/cars/1"],
             "opinions" => [ "api/opinions/1"],
             "mail" => [ "api/mail/1"],
             "notifications" => [ "api/notifications/1"],
             "messages" => [ "api/messages/1"],
             "receiverMessages" => [],
             "driverTrips" => [ ],
             "passengerTrips" => [ ],
             //"tripCount" => 0,
             "trips" => []
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

    // DELETE
    public function testDeleteUser() 
    { 
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        $randomUser = $allUsers[random_int(0, count($allUsers) -1 )];
        $req = static::createClient()->request('DELETE', 'http://localhost/api/users/' . $randomUser->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testPutUser()
    { 
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        $randomUser = $allUsers[random_int(0, count($allUsers) - 1 )];

        $req = static::createClient()->request('PUT', 'http://localhost/api/users/'. $randomUser->getId(), [ 
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                "name"=> "Random",
                "surname" => "Dupont",
                "email" => "connard@gmail.com",
                "phone" => "3630",
                "password" => "tamere",
                "avatar" => "des putes",
                "bio" => "biographie",
                "cars" => [ "api/cars/1"],
                "opinions" => [ "api/opinions/1"],
                "mail" => [ "api/mail/1"],
                "notifications" => [ "api/notifications/1"],
                "messages" => [ "api/messages/1"],
                "receiverMessages" => [],
                "driverTrips" => [ ],
                "passengerTrips" => [ ],
                //"tripCount" => 0,
                "trips" => []
            ])
            ] );

        $this->assertResponseIsSuccessful();
    } 
}
