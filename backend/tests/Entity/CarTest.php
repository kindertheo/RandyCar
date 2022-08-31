<?php

namespace App\Tests;

use App\Entity\Car;
use App\Entity\User;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Utils;

class CarTest extends ApiTestCase
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

    //GET
    public function testGetCar()
    {   
        $req = Utils::request("GET", 'http://localhost/api/cars', []);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("GET", 'http://localhost/api/cars', [], $this->tokenUser);
        $this->assertResponseIsSuccessful();
        
        $req = Utils::request("GET", 'http://localhost/api/cars', [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful();
    }


    //get{id}
    public function testGetById()
    {
        $randomCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);

        $req = Utils::request("GET", 'http://localhost/api/cars/' . $randomCar, []);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("GET", 'http://localhost/api/cars/' . $randomCar, [], $this->tokenUser);
        $this->assertResponseIsSuccessful();
        
        $req = Utils::request("GET", 'http://localhost/api/cars/' . $randomCar, [], $this->tokenAdmin);
        $this->assertResponseIsSuccessful();

        $this->assertResponseIsSuccessful();
    }

    //Put 
    public function testPutCar() 
    {         

        $randomCar = Utils::getRandomIdByCollections(Car::class, $this->entityManager);

        $body = [ 
            "brand" => "testBrand",
            "model" => "modelTest",
            "color" => "colorTest",
            "seatNumber" => random_int(0, 5),
            "licensePlate" => "TEST1234"
        ];

        $req = Utils::request("PUT", "http://localhost/api/cars/" . $randomCar, $body);
        $this->assertResponseStatusCodeSame(401);

        $req = Utils::request("PUT", "http://localhost/api/cars/" . $randomCar, $body, $this->tokenUser);
        $this->assertResponseStatusCodeSame(403);

        $req = Utils::request("PUT", "http://localhost/api/cars/" . $randomCar, $body, $this->tokenAdmin);
        $this->assertResponseIsSuccessful();

        $this->assertResponseIsSuccessful();
    }
}