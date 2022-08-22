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

        $generatedEmail = 'test'.  uniqid() .'@example.com';
        $user = new User();
        $user->setName('test')
        ->setSurname('test')
        ->setPhone('00000000')
        ->setAvatar('')
        ->setBio('test')
        ->setCreatedAt( \DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
        ->setTripCount(0)
        ->setEmail($generatedEmail)
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
                'email' => $generatedEmail,
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
        $faker = Factory::create('fr_FR');

        $client = self::createClient();
        $container = self::getContainer();

        $generatedEmail = 'testAdmin'.  uniqid() .'@example.com';
        $user = new User();
        $user->setName('testAdmin')
        ->setSurname('testAdmin')
        ->setPhone('00000000')
        ->setAvatar('')
        ->setBio('testAdmin')
        ->setCreatedAt( \DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
        ->setTripCount(0)
        ->setEmail($generatedEmail)
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, 'superadminrandycar')
        );
        
        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $generatedEmail,
                'password' => 'superadminrandycar',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(401);

        $client->request('GET', '/admin', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}