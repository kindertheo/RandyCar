<?php

namespace App\Tests;

use App\Entity\Notification;
use App\Entity\User;
use Faker\Factory;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class NotificationTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', 'http://localhost/api/notifications', ['auth_bearer' => $this->tokenAdmin]);

        $queryResult = $this->entityManager
            ->getRepository(Notification::class)
            ->count([]);
        
        $content = $response->getContent();

        $count = json_decode($content, true); 
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUserExtensionUser(): void 
    { 
        $faker = Factory::create('fr_FR');

        $user = $this->entityManager->merge($this->user);
        $numberNotifs = 10;

        for($i=0; $i < $numberNotifs; $i++)
        {
            $notificationEntity = new Notification();
            $notificationEntity
                ->setReceiver($user)
                ->setObject($faker->word())
                ->setContent($faker->word())
                ->setCreatedAt($faker->dateTimeBetween("-200 days", "now") )
                ->setReaded($faker->boolean());
            $this->entityManager->persist($notificationEntity);
        }
        $this->entityManager->flush();   

        $notifCollection = Utils::request("GET", "http://localhost/api/notifications", [], $this->tokenUser);
        $content = $notifCollection->getContent();
        $count = count(json_decode($content, true));
        $this->assertEquals($count, $numberNotifs);


        foreach( json_decode($content, true) as $value )
        {
            $this->assertTrue($value['receiver'] == '/api/users/'. $this->user->getId());
        }
    }


    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/notifications', ["auth_bearer" => $this->tokenUser]);

        $this->assertMatchesResourceCollectionJsonSchema(Notification::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetNotification() 
    {
        $req = Utils::request("GET", 'http://localhost/api/notifications', [], $this->tokenUser); 
        $this->assertResponseIsSuccessful();

        $req = Utils::request("GET", 'http://localhost/api/notifications', [], $this->tokenAdmin); 
        $this->assertResponseIsSuccessful();

        $req = Utils::request("GET", 'http://localhost/api/notifications', []); 
        $this->assertResponseStatusCodeSame(401);
        
    }

    //get{id}
    public function testGetById()
    {
        $id = $this->entityManager->getRepository(Notification::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/notifications/'. $index);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //post 
    public function testPostNotification()
    {

        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[random_int(0, count($randomReceiver)-1 )];

        $body = [ 
            "receiver"=> "api/users/". $randomReceiver->getId(),
            "object"=> "Lorem Ipsum Dolor sit amet",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z",
            "createdAt"=>"2022-08-16T08:22:46.806Z",
            'readed'=> False
        ];

        $req = static::createClient()->request('POST', 'http://localhost/api/notifications', [ 
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

    public function testPutNotification()
    { 
        $randomNotification = $this->entityManager->getRepository(Notification::class)->findAll();
        $randomNotification = $randomNotification[random_int(0, count($randomNotification)-1 )];

        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[random_int(0, count($randomReceiver)-1 )];

        $body = [ 
            "receiver"=> "api/users/". $randomReceiver->getId(),
            "object"=> "Put edition",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z",
            "createdAt"=>"2022-08-16T08:22:46.806Z",
            'readed'=> False
        ];

        $req = static::createClient()->request('PUT', 'http://localhost/api/notifications/' . $randomNotification->getId() , [ 
            'headers' => [ 
                'Content-Type' => 'application/json',
                'accept' => 'application/json'
            ],
            'body' => json_encode($body)
            ]
        );
        $this->assertResponseStatusCodeSame(401);

        //TODO : for user token, need to work ? 
        $user = $this->entityManager->merge($this->user);
        $notification = $user->getNotifications();
        dump($notification[0]); ob_flush(); // is null ?
        $id = $notification[0]->getId();
        dump($id); ob_flush();

        $req = Utils::request('PUT', 'http://localhost/api/notifications/', $id, $this->tokenUser);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request('PUT', 'http://localhost/api/notifications/', $body, $this->tokenAdmin );
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteNotification() 
    { 
        $allId = $this->entityManager->getRepository(Notification::class)->findAll();
        $randomNotification = $allId[random_int(0, count($allId) -1 )];

        // $notification->isReaded();

        $this->assertIsBool($randomNotification->isReaded());
        
        $req = static::createClient()->request('DELETE', 'http://localhost/api/notifications/' . $randomNotification->getId(), ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseIsSuccessful();

        $req = Utils::request('DELETE', 'http://localhost/api/notifications/' . $randomNotification, [], $this->tokenUser);
        $this->assertResponseIsSuccessful();

        $req = Utils::request('DELETE', 'http://localhost/api/notifications/'. $randomNotification, []);
        $this->assertResponseStatusCodeSame(403);

    }

    public function testCreateNotification(){
        $faker = Factory::create('fr_FR');


        $userArray = $this->entityManager->getRepository(User::class)->findAll();
        $user = $userArray[random_int(0, count($userArray) -1 )];
        $user = $this->entityManager->merge($user);

        $words = $faker->word();
        $content = $faker->sentence(10);
        $notificationEntity = new Notification();
        $notificationEntity->setReceiver($user)
            ->setObject($words)
            ->setContent($content)
            ->setCreatedAt(new \DateTime('now'))
            ->setReaded(false);

        $this->entityManager->persist($notificationEntity);
        $this->entityManager->flush();

        $notification = $this->entityManager->getRepository(Notification::class)->findOneBy(['object' => $words, 'content' => $content]);

        $this->assertNotNull($notification);
    }

}