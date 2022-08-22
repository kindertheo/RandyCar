<?php

namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Faker\Factory;


class Utils extends ApiTestCase
{

    public static function getRandomIdByCollections($class, $entityManager): int
        {
            $collections =  $entityManager->getRepository($class)->findAll();
            $random = $collections[random_int(0, count($collections) - 1 )];
            return $random->getId();
        }  

    public static function getToken(User $user): string
    {
        $client = self::createClient();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $user->getEmail(),
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        return $json['token'];
    }

    public static function deleteUser($user, $entityManager): void
    {
        $entity = $entityManager->merge($user);
        $entityManager->remove($entity);
        $entityManager->flush();
    }

    public static function createUser(bool $isAdmin): User
    {
        $faker = Factory::create('fr_FR');


        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        
        $generatedEmail = 'test'.  uniqid() .'@example.com';
        if($isAdmin){
             $user->setRoles(['ROLE_ADMIN']);
             $generatedEmail = 'testAdmin'.  uniqid() .'@example.com';
        }
        
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

        return $user;
    }


    public static function request(string $method, string $url,  array $body = [], string $token = Null){
        $headers = [ 
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ];
        if($token){
            $headers['Authorization'] = 'bearer ' . $token;
        }

        $req = static::createClient()->request($method, $url, [ 
            'headers' => $headers,
            'body' => json_encode($body)
        ]);

        return $req;
    }


}
