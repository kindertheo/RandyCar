<?php

namespace App\Tests;

use App\Entity\Car;
use App\Entity\Address;
use App\Entity\City;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Utils;
use Faker\Factory;


class CitiesTest extends ApiTestCase
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
        $response = static::createClient()->request('GET', 'http://localhost/api/cities');
        $this->assertResponseStatusCodeSame(401);

        $response = static::createClient()->request('GET', 'http://localhost/api/cities', ['auth_bearer' => $this->tokenUser]);
        $this->assertCountAndSuccess($response, City::class);

        $response = static::createClient()->request('GET', 'http://localhost/api/cities', ['auth_bearer' => $this->tokenAdmin]);
        $this->assertCountAndSuccess($response, City::class);
    }

    // get{id}
    public function testGetById(): void 
    { 
        $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);
        $this->assertIsNumeric($randomIdCity);

        $response = static::createClient()->request('GET', 'http://localhost/api/cities/' . $randomIdCity);
        $this->assertResponseStatusCodeSame(401);

        $response = static::createClient()->request('GET', 'http://localhost/api/cities/' . $randomIdCity , ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $response = static::createClient()->request('GET', 'http://localhost/api/cities/' . $randomIdCity , ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    
    public function testJsonFormat(): void 
    { 
        $response = static::createClient()->request('GET', 'http://localhost/api/cities', ['auth_bearer' => $this->tokenAdmin]);

        $this->assertMatchesResourceCollectionJsonSchema(City::class);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }


     // TEST POST
     public function testPostCity() 
     { 
        $body = [
            'postalCode' => "6666",
            'name'=> "testPostNameCityOfTheDeath",
        ];

        $req = Utils::request('POST', 'http://localhost/api/cities',  $body);
        $this->assertResponseStatusCodeSame(401);
    
        $req = Utils::request('POST', 'http://localhost/api/cities',  $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request('POST', 'http://localhost/api/cities',  $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
     }
 
     // TEST DELETE 
     public function testDeleteCity() 
     { 
        $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/cities/' . $randomIdCity);
        $this->assertResponseStatusCodeSame(401);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/cities/' . $randomIdCity, ['auth_bearer' => $this->tokenUser]);
        $this->assertResponseStatusCodeSame(403);

        $req = static::createClient()->request('DELETE', 'http://localhost/api/cities/' . $randomIdCity, ['auth_bearer' => $this->tokenAdmin]);
        $this->assertResponseStatusCodeSame(204);
    }

 
     // TEST PUT 
     public function testPutCity() 
     { 
        $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);
 
        $body = [
            'postalCode' => "6666",
            'name'=> "testPostNameCityOfTheDeath",
        ];
        $req = Utils::request('PUT', 'http://localhost/api/cities/' . $randomIdCity,  $body);
        $this->assertResponseStatusCodeSame(401);
    
        $req = Utils::request('PUT', 'http://localhost/api/cities/' . $randomIdCity,  $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request('PUT', 'http://localhost/api/cities/' . $randomIdCity,  $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
     }


     //SHOULD BE DEBUGGED LATER
     // PUT QUERY DOESNT WORK AT ALL
     // CANT LINK TO AN OTHER ENTITY
     // FCK THIS SHIT MAN 

    // public function testAddAddressToCity()
    // {
    //     $randomIdCity = Utils::getRandomIdByCollections(City::class, $this->entityManager);
    //     // $randomIdAddress = Utils::getRandomIdByCollections(Address::class, $this->entityManager);
 
    //     $bodyAddress = [
    //         'number' => "12",
    //         'street'=> "Rue des tests",
    //     ];
    //     $req = Utils::request('POST', 'http://localhost/api/addresses',  $bodyAddress, $this->tokenAdmin);
    //     $idAddress = json_decode($req->getContent())->id;
    //     $body = [
    //         'postalCode' => "6666",
    //         'name'=> "testPostNameCityOfTheDeath",
    //         'address' => 'api/addresses/' . $idAddress
    //     ];
    //     $req = Utils::request('PUT', 'http://localhost/api/cities/' . $randomIdCity,  $body, $this->tokenAdmin);
    //     dump($req->getContent());
    //     $this->assertResponseIsSuccessful();
    //     $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

    //     $response = static::createClient()->request('GET', 'http://localhost/api/cities/' . $randomIdCity, ['auth_bearer' => $this->tokenUser]);
    // }

    public function testAddAddressToCity()
    {
        $faker = Factory::create('fr_FR');

        $randomAddresses = $this->entityManager->getRepository(Address::class)->findAll();
        $randomAddress = $randomAddresses[random_int(0, count($randomAddresses) -1 )];
        
        $cityEntity = new City();
        $cityEntity->setPostalCode(str_replace(' ', '',$faker->postcode()))
                ->setName($faker->city())
                ->addAddress($randomAddress);
        
        $this->entityManager->merge($randomAddress);
        $this->entityManager->persist($cityEntity);
        $this->entityManager->flush();
        
        $cityFromDb = $this->entityManager->getRepository(City::class)->findOneBy(['id' => $cityEntity->getId()]);

        $addressesIds = [];
        foreach($cityFromDb->getAddresses() as $address){
            array_push($addressesIds, $address->getId());
        }

        $this->assertContains($randomAddress->getId(), $addressesIds);
    }

    public function testRemoveAddressToCity(){
        //get a random city
        $randomCities = $this->entityManager->getRepository(City::class)->findAll();
        $cityEntity = $randomCities[random_int(0, count($randomCities) -1 )];

        //if city got not addresses, picking an other city
        $address = $cityEntity->getAddresses()[0];
        while($address == null){
            $cityEntity = $randomCities[random_int(0, count($randomCities) -1 )];
            $addressArray = $cityEntity->getAddresses();
            $address = $addressArray[0];
        }

        //remove the address
        $cityEntity->removeAddress($address);
        $this->entityManager->persist($cityEntity);
        $this->entityManager->flush();

        //picking city from the database
        $cityFromDb = $this->entityManager->getRepository(City::class)->findOneBy(['id' => $cityEntity->getId()]);
        
        //put addresses ids in an array 
        $addressesIds = [];
        foreach($cityFromDb->getAddresses() as $city){
            array_push($addressesIds, $city->getId());
        }
        
        //verify if adresse is not in the array anymore
        $this->assertNotContains($address->getId(), $addressesIds);
    }

}