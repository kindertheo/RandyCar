<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Fuel;
use App\Entity\Car;
use App\Entity\City;
use Doctrine\ORM\QueryBuilder;
use App\Tests\Utils;


class FuelTest extends ApiTestCase
{
    private $entityManager;

    private $tokenAdmin;
    private $tokenUser;

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

        Utils::deleteUser($this->admin, $this->entityManager);
        Utils::deleteUser($this->user, $this->entityManager);

        $this->entityManager->close();
        $this->entityManager = null;
    }

    private function requestFuel(string $method, string $url, int $randomIdCar, string $token = Null){
        $headers = [ 
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ];
        if($token){
            $headers['Authorization'] = 'bearer ' . $token;
        }

        $req = static::createClient()->request($method, $url, [ 
            'headers' => $headers,
            'body' => json_encode([
                'name' => "TestPut",
                'consumption'=> 6.66,
                'cars' => ["api/cars/". $randomIdCar],
            ])
        ]);

        return $req;
    }


    //GET
    public function testGetFuelUser()
    {
        //test with Auth Admin
        $req = static::createClient()->request('GET', 'http://localhost/api/fuels', ['auth_bearer' => $this->tokenUser]);
        $queryResult = $this->entityManager 
            ->getRepository(Fuel::class)
            ->count([]);

        $content = $req->getContent();
        $count = json_decode($content, true);
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //GET
    public function testGetFuelAdmin()
    {
        //test with Auth Admin
        $req = static::createClient()->request('GET', 'http://localhost/api/fuels', ['auth_bearer' => $this->tokenAdmin]);
        $queryResult = $this->entityManager 
            ->getRepository(Fuel::class)
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
        $response = static::createClient()->request('GET', 'http://localhost/api/fuels', ['auth_bearer' => $this->tokenUser] );

        $this->assertMatchesResourceCollectionJsonSchema(Fuel::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    //get{id}
    public function testGetById()
    {
        // findAll
        $id = $this->entityManager->getRepository(Fuel::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        // use Index as slug
        $response = static::createClient()->request('GET', 'http://localhost/api/fuels/'. $index);
        $this->assertResponseStatusCodeSame(401);

        $response = static::createClient()->request('GET', 'http://localhost/api/fuels/'. $index, ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


        $response = static::createClient()->request('GET', 'http://localhost/api/fuels/'. $index, ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


     // TEST POST
     public function testPostFuel() 
     { 
        $randomIdCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);

        //Without Token => Expected error : Token not found
        $req = $this->requestFuel('POST', 'http://localhost/api/fuels', $randomIdCar);
        $this->assertResponseStatusCodeSame(401);

        //With User token => Access Denied
        $req = $this->requestFuel('POST', 'http://localhost/api/fuels', $randomIdCar, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        //With Admin Token => Success
        $req = $this->requestFuel('POST', 'http://localhost/api/fuels', $randomIdCar, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
     }
 
     // TEST DELETE 
     public function testDeleteFuel() 
     { 
        $randomIdFuel = Utils::getRandomIdByCollections(Fuel::class, $this->entityManager);

        //No Token Found
        $req = static::createClient()->request('DELETE', 'http://localhost/api/fuels/' . $randomIdFuel);
        $this->assertResponseStatusCodeSame(401);

        //Access Denied
        $req = static::createClient()->request('DELETE', 'http://localhost/api/fuels/' . $randomIdFuel, ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseStatusCodeSame(403);

        //Auth
        $req = static::createClient()->request('DELETE', 'http://localhost/api/fuels/' . $randomIdFuel, ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseStatusCodeSame(204);        
    }

     // TEST PUT 
     public function testPutFuel() 
     { 
        $randomIdCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);
        $randomIdFuel = Utils::getRandomIdByCollections(Fuel::class, $this->entityManager);
 
        //Token not found
        $req = $this->requestFuel('PUT', 'http://localhost/api/fuels/' . $randomIdFuel, $randomIdCar);
        $this->assertResponseStatusCodeSame(401);

        //Access Denied
        $req = $this->requestFuel('PUT', 'http://localhost/api/fuels/' . $randomIdFuel, $randomIdCar, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        //Is Auth
         $req =  $this->requestFuel('PUT', 'http://localhost/api/fuels/' . $randomIdFuel, $randomIdCar, $this->tokenAdmin);
         $this->assertResponseIsSuccessful();
     }
}