<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Address;
use App\Entity\City;
use App\Entity\Trip;
use App\Repository\AddressRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\VarDumper\VarDumper;
use App\Tests\Utils;


class AddressTest extends ApiTestCase
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

        Utils::deleteUser($this->admin, $this->entityManager);
        Utils::deleteUser($this->user, $this->entityManager);

        $this->entityManager->close();
        $this->entityManager = null;
    }

    private function assertCountAndSuccess($response, string $entity)
    {
        $queryResult = $this->entityManager
        ->getRepository($entity)
        ->count([]);
        
        $content = $response->getContent();

        $count = json_decode($content, true); 
        $count = $count['hydra:totalItems'];

        $this->assertEquals($count, $queryResult);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testCountAndSuccess(): void
    {
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses');
        $this->assertResponseStatusCodeSame(401);

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses', ['auth_bearer' => $this->tokenUser]);
        $this->assertCountAndSuccess($response, Address::class);

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses', ['auth_bearer' => $this->tokenAdmin]);
        $this->assertCountAndSuccess($response, Address::class);
    }

    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/addresses', ['auth_bearer' => $this->tokenUser]);
        $this->assertMatchesResourceCollectionJsonSchema(Address::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses', ['auth_bearer' => $this->tokenAdmin]);
        $this->assertMatchesResourceCollectionJsonSchema(Address::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST GET{ID}
    public function testgetById(): void 
    { 
        // findAll
        $id = $this->entityManager->getRepository(Address::class)->findAll();
        $objectId = $id[0];
        $this->assertIsObject($objectId); 

        //get Object + index separate
        $index = $objectId->getId(); 
        $this->assertIsNumeric($index);

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses/'. $index);
        $this->assertResponseStatusCodeSame(401);

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses/'. $index, ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $response = static::createClient()->request('GET', 'http://localhost/api/addresses/'. $index, ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    // TEST POST
    public function testPostAddress() 
    { 
        $randomIdCities = Utils::getRandomIdByCollections(City::class, $this->entityManager);
        $body = [
            'number' => "6",
            'street'=> "Rue de John Doe",
            'city' => "api/cities/". $randomIdCities,
            'trips' => []
        ];
        $req = Utils::request('POST', 'http://localhost/api/addresses',  $body);
        $this->assertResponseStatusCodeSame(401);
    
        $req = Utils::request('POST', 'http://localhost/api/addresses',  $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request('POST', 'http://localhost/api/addresses',  $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    // TEST DELETE 
    public function testDeleteAddress() 
    { 
        $allId = $this->entityManager->getRepository(Address::class)->findAll();
        $randomAddress = $allId[random_int(0, count($allId) -1 )];

        $req = static::createClient()->request('DELETE', 'http://localhost/api/addresses/' . $randomAddress->getId());
        $this->assertResponseStatusCodeSame(401);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/addresses/' . $randomAddress->getId(), ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseStatusCodeSame(403);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/addresses/' . $randomAddress->getId(), ['auth_bearer' => $this->tokenAdmin]);
        $countStart = count($randomAddress->getStartTrips());
        $countEnd = count($randomAddress->getEndTrips());

        //var_dump($count);
        if( $countStart == 0 && $countEnd == 0 ){
            $this->assertResponseStatusCodeSame(204);
        } else {
            $this->assertResponseStatusCodeSame(500);
        }
    }

    // TEST PUT 
    public function testPutAddress() 
    { 
        $randomAddress = Utils::getRandomIdByCollections(Address::class, $this->entityManager);
        $randomCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);
        $randomTrip = Utils::getRandomIdByCollections(Trip::class, $this->entityManager);

        $body = [
            'number' => "6",
            'street'=> "Rue de John Doe",
            'city' => "api/cities/" .$randomCity,
            'trips' => ["api/trips/" .$randomTrip]
        ];
        $req = Utils::request('PUT', 'http://localhost/api/addresses/' . $randomAddress,  $body);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request('PUT', 'http://localhost/api/addresses/' . $randomAddress,  $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request('PUT', 'http://localhost/api/addresses/' . $randomAddress,  $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

}
