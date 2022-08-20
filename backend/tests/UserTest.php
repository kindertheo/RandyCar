<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Mail;
use App\Entity\Messages;
use App\Entity\Notification;
use App\Entity\Opinion;
use App\Entity\Car;
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
        
        $carId = $this->getRandomIdByCollections(Car::class);
        $opinionId = $this->getRandomIdByCollections(Opinion::class);
        $mailId = $this->getRandomIdByCollections(Mail::class);
        $notifId = $this->getRandomIdByCollections(Notification::class);
        $messageId = $this->getRandomIdByCollections(Messages::class);

         $body = [
             "name"=> "Random",
             "surname" => "Dupont",
             "email" => "connard". uniqid() ."@gmail.com",
             "phone" => "3630",
             "password" => "tamere",
             "avatar" => "des putes",
             "bio" => "biographie",
            "cars" => [ "api/cars/" . $carId ],
            "opinions" => [ "api/opinions/" . $opinionId],
            "mail" => [ "api/mail/" . $mailId],
            "notifications" => [ "api/notifications/" . $notifId],
            "messages" => [ "api/messages/" . $messageId],
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

    private function getRandomIdByCollections($class)
    {
        $collections =  $this->entityManager->getRepository($class)->findAll();
        $random = $collections[random_int(0, count($collections) - 1 )];
        return $random->getId();
    }
    // PUT
    public function testPutUser()
    { 
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        $randomUser = $allUsers[random_int(0, count($allUsers) - 1 )];

        $carId = $this->getRandomIdByCollections(Car::class);
        $opinionId = $this->getRandomIdByCollections(Opinion::class);
        $mailId = $this->getRandomIdByCollections(Mail::class);
        $notifId = $this->getRandomIdByCollections(Notification::class);
        $messageId = $this->getRandomIdByCollections(Messages::class);



        $req = static::createClient()->request('PUT', 'http://localhost/api/users/'. $randomUser->getId(), [ 
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode([
                "name"=> "Random",
                "surname" => "Dupont",
                "email" => "connard" . uniqid() . "@gmail.com",
                "phone" => "3630",
                "password" => "tamere",
                "avatar" => "des putes",
                "bio" => "biographie",
                "cars" => [ "api/cars/" . $carId ],
                "opinions" => [ "api/opinions/" . $opinionId],
                "mail" => [ "api/mail/" . $mailId],
                "notifications" => [ "api/notifications/" . $notifId],
                "messages" => [ "api/messages/" . $messageId],
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
