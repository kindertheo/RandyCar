<?php

namespace App\Tests;

use App\Entity\Mail;
use App\Entity\User;
use Faker\Factory;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class MailTest extends ApiTestCase
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

    // testJson 
    public function testJsonFormat(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/mails', ["auth_bearer" => $this->tokenUser]);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetMail() 
    {
        $req = Utils::request("GET", 'http://localhost/api/mails', []);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("GET", 'http://localhost/api/mails', [], $this->tokenUser);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("GET", 'http://localhost/api/mails', [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
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
    public function testPostMail()
    {

        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[random_int(0, count($randomReceiver)-1 )];

        $body = [ 
            "receiver"=> "api/users/". $randomReceiver->getId(),
            "object"=> "Lorem Ipsum Dolor sit amet",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z"
        ];

        $req = static::createClient()->request('POST', 'http://localhost/api/mails', [ 
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

    public function testPutMail()
    { 
        $randomMail = $this->entityManager->getRepository(Mail::class)->findAll();
        $randomMail = $randomMail[random_int(0, count($randomMail)-1 )];

        $randomReceiver = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceiver = $randomReceiver[random_int(0, count($randomReceiver)-1 )];

        $body = [ 
            "receiver"=> "api/users/". $randomReceiver->getId(),
            "object"=> "Put edition",
            "content"=> "This is a lorem ipsum content. If you see this that mean post testing is working correctly on this project",
            "sentDate"=> "2022-08-16T08:22:46.806Z"
        ];

        $req = static::createClient()->request('PUT', 'http://localhost/api/mails/' . $randomMail->getId() , [ 
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

    public function testDeleteMail() 
    { 
        $allId = $this->entityManager->getRepository(Mail::class)->findAll();
        $randomOpinion = $allId[random_int(0, count($allId) -1 )];
        $req = static::createClient()->request('DELETE', 'http://localhost/api/mails/' . $randomOpinion->getId());
        $this->assertResponseIsSuccessful();
    }

    
    public function testCreateMail(){
        $faker = Factory::create('fr_FR');


        $userArray = $this->entityManager->getRepository(User::class)->findAll();
        $user = $userArray[random_int(0, count($userArray) -1 )];

        $words = $faker->word();
        $content = $faker->sentence(10);
        $mailEntity = new Mail();
        $mailEntity->setReceiver($user)
            ->setObject($words)
            ->setContent($content)
            ->setSentDate(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ));

        $this->entityManager->persist($mailEntity);
        $this->entityManager->flush();

        $mail = $this->entityManager->getRepository(Mail::class)->findOneBy(['object' => $words, 'content' => $content]);

        $this->assertNotNull($mail);
    }
}