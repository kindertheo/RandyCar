<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Mail;
use App\Entity\Messages;
use App\Entity\Notification;
use App\Entity\Opinion;
use App\Entity\Car;
use App\Tests\Utils;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    protected function setUp(): void 
    { 
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->admin = Utils::createUser(True);
        $this->user = Utils::createUser(False);

        $this->tokenAdmin = Utils::getToken($this->admin);
        $this->tokenUser = Utils::getToken($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCountAndSuccess(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/users', [ 'auth_bearer' => $this->tokenUser]);

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
        $response = static::createClient()->request('GET', 'http://localhost/api/users', ["auth_bearer" => $this->tokenUser]);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST GET{ID}
    public function testgetById(): void 
    { 
        $randomUser = Utils::getRandomIdByCollections(User::class, $this->entityManager);

        $req = Utils::request("GET", 'http://localhost/api/users/' . $randomUser, []);
        $this->assertResponseStatusCodeSame(401); // jwt not found

        $req = Utils::request("GET", 'http://localhost/api/users/' . $randomUser, [], $this->tokenUser);
        $this->assertResponseIsSuccessful(204);

        $req = Utils::request("GET", 'http://localhost/api/users/' . $randomUser, [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful(204);
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

        $randomId = Utils::getRandomIdByCollections(User::class, $this->entityManager);

        $req = Utils::request("DELETE", 'http://localhost/api/users/' . $randomId, [], $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request("DELETE", 'http://localhost/api/users/'. $randomId, [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful(204);

        $req = Utils::request("DELETE", 'http://localhost/api/users/'. $this->user->getId(), [], $this->tokenUser);
        // method not allowed ? 
        $this->assertResponseIsSuccessful(204);
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
        $body = [ 
            "name"=> "Random",
            "surname" => "Dupont",
            "email" => "connard" . uniqid() . "@gmail.com",
            "phone" => "3630",
            "password" => "tamere",
            "avatar" => "des putes",
            "bio" => "biographie",
            "cars" => [ "api/cars/" . Utils::getRandomIdByCollections(Car::class, $this->entityManager) ],
            "opinions" => [ "api/opinions/" . Utils::getRandomIdByCollections(Opinion::class, $this->entityManager)],
            "mail" => [ "api/mail/" . Utils::getRandomIdByCollections(Mail::class, $this->entityManager)],
            "notifications" => [ "api/notifications/" . Utils::getRandomIdByCollections(Notification::class, $this->entityManager)],
            "messages" => [ "api/messages/" . Utils::getRandomIdByCollections(Messages::class, $this->entityManager)],
            "receiverMessages" => [],
            "driverTrips" => [ ],
            "passengerTrips" => [ ],
            "trips" => []
        ];
        
        $randomUser = Utils::request("PUT", 'http://localhost/api/users/' . $this->user->getId(), $body, $this->tokenUser);
    
        $this->assertResponseIsSuccessful();
    } 
}
