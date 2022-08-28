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
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCountAndSuccess(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/notifications');

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

    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/notifications');

        $this->assertMatchesResourceCollectionJsonSchema(Notification::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetNotification() 
    {
        $req = static::createClient()->request('GET', 'http://localhost/api/notifications');
        $queryResult = $this->entityManager 
            ->getRepository(Notification::class)
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
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    public function testDeleteNotification() 
    { 
        $allId = $this->entityManager->getRepository(Notification::class)->findAll();
        $randomNotification = $allId[random_int(0, count($allId) -1 )];
        // $notification->isReaded();
        $this->assertIsBool($randomNotification->isReaded());
        $req = static::createClient()->request('DELETE', 'http://localhost/api/notifications/' . $randomNotification->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testCreateNotification(){
        $faker = Factory::create('fr_FR');


        $userArray = $this->entityManager->getRepository(User::class)->findAll();
        $user = $userArray[random_int(0, count($userArray) -1 )];

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