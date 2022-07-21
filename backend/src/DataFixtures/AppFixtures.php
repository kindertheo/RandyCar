<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Fuel;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\City;
use App\Entity\Address;
use App\Entity\Messages;
use App\Entity\Notification;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');


        //User
        $userArray = [];
        
        for($i=0; $i <= 50; $i++){
            $userEntity = new User();

            $phoneNumber = '0';
            $phoneNumber .= $faker->numberBetween(1,6);
            $phoneNumber .= $faker->regexify('[1-5]{6}');
            // echo $phoneNumber;
            $userEntity->setName( $faker->name() )
                ->setSurname( $faker->lastName() )
                ->setEmail( $faker->email() )
                ->setPhone( $phoneNumber )
                ->setPassword( $faker->password() )
                ->setAvatar('')
                ->setBio( $faker->sentence(20) )
                ->setCreatedAt( \DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
                ->setTripCount(0);
            
            array_push($userArray, $userEntity);
            $manager->persist($userEntity);
        }


        //Fuel
        $fuelType = ['SP95E10', 'SP95', 'SP98', 'Gazole', 'GPL-c', 'Hybride', 'Electrique'];
        $fuelArray = [];

        for($i=0; $i <= count($fuelType) - 1; $i++){
            $fuelEntity = new Fuel();

            $fuelEntity->setName($fuelType[$i])
                ->setConsumption($faker->randomFloat(2, 1, 5));
            
            array_push($fuelArray, $fuelEntity);
            $manager->persist($fuelEntity);
        }

        //Car
        $carArray = [];

        for($j=0; $j <= 30; $j++){
            $carEntity = new Car();

            $carEntity->setBrand( $faker->word( ))
                    ->setModel( $faker->word() )
                    ->setColor( $faker->colorName() )
                    ->setSeatNumber( random_int(3, 5))
                    ->setLicensePlate( strtoupper($faker->regexify('[A-Za-z0-9]{6}')) )
                    ->setOwner( $userArray[random_int(0, count($userArray) - 1 )])
                    ->setFuel(  $fuelArray[random_int(0, count($fuelArray) - 1 )]);
            
            array_push($carArray, $carEntity);
            $manager->persist($carEntity);

        }

        //City 
        $cityArray = [];

        for($l =0; $l<=10; $l++) { 
            $cityEntity = new City();
            $cityEntity->setPostalCode(str_replace(' ', '',$faker->postcode()))
                ->setName($faker->city());

            array_push($cityArray, $cityEntity);
            $manager->persist($cityEntity);
        }

        //Address
        $addressArray = [];

        for($k=0;$k<=20;$k++) { 
            $addressEntity = new Address();

            $addressEntity->setNumber($faker->buildingNumber())
                ->setStreet($faker->streetName())
                ->setCityId(  $cityArray[random_int(0, count($cityArray) - 1 )]);
            
            array_push($addressArray, $addressEntity);
            $manager->persist($addressEntity);
        }

        //Messages 
        $messageArray = [];

        for($m=0;$m<=20;$m++) { 
            $messageEntity = new Messages();
            $messageEntity->setCreatedAt(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
                ->setContent($faker->realText())
                ->setAuthor($userArray[random_int(0, count($userArray) - 1 )])
                ->setReceiver($userArray[random_int(0, count($userArray) - 1 )])
                ->setIsRead($faker->dateTimeBetween('-200 days', 'now'));

            array_push($messageArray, $messageEntity);
            $manager->persist($messageEntity);
        }

        //Notifications 
        $notificationArray = [];

        for($n = 0; $n<20; $n++) { 
            $notificationEntity = new Notification();

            $notificationEntity
                // ->setReceiver($userArray[random_int(0, count($userArray) - 1 )])
                // ->setObject($faker->word())
                // ->setContent($faker->word())
                // ->setCreatedAt($faker->dateTimeBetween("-200 days", "now") )
                // ->setRead($faker->boolean());
                ->setRead(true);

            array_push($notificationArray, $notificationEntity);
            $manager->persist($notificationEntity);
        }





        $manager->flush();

    }
}
