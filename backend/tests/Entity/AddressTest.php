<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Address;
use App\Entity\City;
use App\Entity\User;
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
        $this->assertCountAndSuccess($response, Address::class);

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
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

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



    private function creatingAnAddress(bool $startTrip = True): array
    {
        //Get non-admin users
        $allUsers = $this->entityManager->createQueryBuilder('u')
        ->select('u')
        ->from(User::class, 'u')
        ->where('u.roles NOT LIKE :role')
        ->setParameter('role', '%ROLE_ADMIN%')
        ->getQuery()
        ->getResult();
        $randomUser = $allUsers[random_int(0, count($allUsers) -1 )];

        //Create a new Trip to add to the Address
        $newTrip = new Trip();
        $newTrip->setMaxPassenger(3)
            ->setDateStart(new \DateTime('now'))
            ->setIsFinished(False)
            ->setIsCancelled(False)
            ->setDriver($randomUser);

        //Merge and Persist Trip!
        $tripEntity = $this->entityManager->merge($newTrip);
        $this->entityManager->persist($tripEntity);

        //Find a random City  to add to the Address
        $allCities = $this->entityManager->getRepository(City::class)->findAll();
        $randomCity = $allCities[random_int(0, count($allCities) -1 )];

        //Merge and Persist City!
        $cityEntity = $this->entityManager->merge($randomCity);
        $this->entityManager->persist($randomCity);

        //Creating the Address and flushing it
        $address = new Address();
        $address->setNumber('123456')
            ->setStreet('StreetTest')
            ->setCity($randomCity);

        if($startTrip){
            $address->addStartTrip($tripEntity);
        }else{
            $address->addEndTrip($tripEntity);
        }
        $this->entityManager->persist($address);
        $this->entityManager->flush(); 
        
        return ['address' => $address, 'trip' => $tripEntity];
    }

    private function getTripsIds(Address $address, bool $startTrips = True): array
    {
        //Get the Address from the database
        $address = $this->entityManager->getRepository(Address::class)->findOneBy(['id' => $address->getId()]);

        //Put all trips id in an array
        if($startTrips){
            $trips = $address->getStartTrips();
        }else{
            $trips = $address->getEndTrips();
        }
        $tripsIds = [];
        foreach($trips as $trip){
            array_push($tripsIds, $trip->getId());
        }

        return $tripsIds; 
    }

    //UNIT TESTS
    public function testAddStartTrips(){
        //Get address and trip from upside
        $entities = $this->creatingAnAddress(True);
        $address = $entities['address'];
        $tripEntity = $entities['trip'];


        $tripsIds = $this->getTripsIds($address, True);
        //Verify our freshly created trip's id is in it!
        $this->assertContains($tripEntity->getId(), $tripsIds);
    }

    public function testRemoveStartTrips(){
        //Get address and trip from upside
        $entities = $this->creatingAnAddress(True);
        $address = $entities['address'];
        $tripEntity = $entities['trip'];

        $address->removeStartTrip($tripEntity);
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        $tripsIds = $this->getTripsIds($address, True);

        //Verify our freshly created trip's id is in it!
        $this->assertNotContains($tripEntity->getId(), $tripsIds);
    }

    public function testAddEndTrips(){
        //Get address and trip from upside
        $entities = $this->creatingAnAddress(False);
        $address = $entities['address'];
        $tripEntity = $entities['trip'];

        $tripsIds = $this->getTripsIds($address, False);

        //Verify our freshly created trip's id is in it!
        $this->assertContains($tripEntity->getId(), $tripsIds);
    }

    public function testRemoveEndTrips(){
        //Get address and trip from upside
        $entities = $this->creatingAnAddress(False);
        $address = $entities['address'];
        $tripEntity = $entities['trip'];

        $address->removeEndTrip($tripEntity);
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        $tripsIds = $this->getTripsIds($address, False);

        //Verify our freshly created trip's id is in it!
        $this->assertNotContains($tripEntity->getId(), $tripsIds);
    }
    


}
