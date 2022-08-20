<?php
// tests/AuthenticationTest.php

namespace App\Tests;

use Faker\Factory;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
// use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class AuthenticationTest extends ApiTestCase
{
    // use ReloadDatabaseTrait;

    public function testLogin(): void
    {
        $faker = Factory::create('fr_FR');


        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        $user->setName('test')
        ->setSurname('test')
        ->setPhone('00000000')
        ->setAvatar('')
        ->setBio('test')
        ->setCreatedAt( \DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
        ->setTripCount(0)
        ->setEmail('test'.  uniqid() .'@example.com')
        ->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
        );

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/test_auth');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/test_auth', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }


    public function testLoginAdmin(): void
    {
        $client = self::createClient();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@randycar.fr',
                'password' => 'superadminrandycar',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/admin', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}