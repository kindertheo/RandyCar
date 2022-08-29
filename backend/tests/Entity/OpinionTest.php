<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Opinion;
use App\Entity\User;
use Faker\Factory;
use App\Tests\Utils;


class OpinionTest extends ApiTestCase
{
    private $entityManager;

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

    //GET
    public function testGetOpinions()
    {

        $req = Utils::request("GET", 'http://localhost/api/opinions', [], $this->tokenUser); 
        $this->assertResponseIsSuccessful();

        $req = Utils::request("GET", 'http://localhost/api/opinions', [], $this->tokenAdmin); 
        $this->assertResponseIsSuccessful();

        $req = Utils::request("GET", 'http://localhost/api/opinions', []); 
        $this->assertResponseStatusCodeSame(401);

    }

    // testJson 
    public function testJsonFormat(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/users', ["auth_bearer" => $this->tokenUser]);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
    
    //get{id}
    public function testGetById()
    {

        $random = Utils::getRandomIdByCollections(Opinion::class, $this->entityManager);
        $req = Utils::request('GET', "http://localhost/api/opinions/". $random, []);
        $this->assertResponseStatusCodeSame(401);
        
        $req = Utils::request('GET', "http://localhost/api/opinions/". $random, [], $this->tokenUser);
        $this->assertResponseIsSuccessful();

        $req = Utils::request('GET', "http://localhost/api/opinions/". $random, [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
    }

    //Put 
    public function testPutOpinions() 
    {         
        $body = [ 
            "message" => "testPut"
        ];

        $randomOpinion = Utils::getRandomIdByCollections(Opinion::class, $this->entityManager);

        $req = Utils::request('PUT', 'http://localhost/api/opinions/'. $randomOpinion, $body);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request('PUT', 'http://localhost/api/opinions/'. $randomOpinion, $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request('PUT', 'http://localhost/api/opinions/'. $randomOpinion, $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful(204);

    }

    // TEST DELETE 
    public function testDeleteOpinions() 
    { 
        // $allId = $this->entityManager->getRepository(Opinion::class)->findAll();
        // $randomOpinion = $allId[random_int(0, count($allId) -1 )];
        // $req = static::createClient()->request('DELETE', 'http://localhost/api/opinions/' . $randomOpinion->getId());
        // $this->assertResponseIsSuccessful();
    
        $random = Utils::getRandomIdByCollections(Opinion::class, $this->entityManager);

        $req = Utils::request("DELETE", 'http://localhost/api/opinions/'. $random, []);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("DELETE", 'http://localhost/api/opinions/'. $random, [], $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request("DELETE", 'http://localhost/api/opinions/'. $random, [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
    }

    // post 
    // TODO : Voter for post Opinion emitter and receptor
    public function testPostOpinion() { 

        $randomEmitter = Utils::getRandomIdByCollections(User::class, $this->entityManager);
        $randomReceptor = Utils::getRandomIdByCollections(User::class, $this->entityManager); 

        $body = [ 
            "notation"=> 2,
            "message"=> "hello world",
            "emitter"=> "api/users/" . $randomEmitter,
            "receptor"=> "api/users/" . $randomReceptor,
            "createdAt"=> "2022-08-16T08:22:46.806Z"
        ];

        $req = Utils::request('POST', 'api/opinions', $body);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request('POST', 'api/opinions', $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(201);

        $req = Utils::request('POST', 'api/opinions', $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    public function testCreateOpinion(){

        $faker = Factory::create('fr_FR');

        $randomEmitter = $this->entityManager->getRepository(User::class)->findAll();
        $randomEmitter = $randomEmitter[random_int(0, count($randomEmitter)-1)];
        $randomReceptor = $this->entityManager->getRepository(User::class)->findAll();
        $randomReceptor = $randomReceptor[random_int(0, count($randomReceptor)-1)];
        
        $content = $faker->sentence(10);

        $randomEmitter = $this->entityManager->merge($randomEmitter);
        $randomReceptor = $this->entityManager->merge($randomReceptor);
        $this->entityManager->persist($randomEmitter);
        $this->entityManager->persist($randomReceptor);

        $opinionEntity = new Opinion();
        $opinionEntity->setEmitter($randomEmitter)
            ->setReceptor($randomReceptor)
            ->setMessage($content)
            ->setCreatedAt(new \DateTime('now'))
            ->setNotation(random_int(1,5));

        $this->entityManager->persist($opinionEntity);
        $this->entityManager->flush();

        $opinion = $this->entityManager->getRepository(Opinion::class)->findOneBy(['message' => $content]);

        $this->assertNotNull($opinion);
    }
}