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
use App\Entity\Mail;
use App\Entity\Messages;
use App\Entity\Notification;
use App\Entity\Opinion;
use App\Entity\Trip;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');


        //User
        $userArray = [];

        $userAdminEntity = new User();

        
        $plaintextPassword = 'superadminrandycar';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $userAdminEntity,
            $plaintextPassword
        );

        $userAdminEntity->setName( 'Admin' )
            ->setSurname( 'RandyStaff' )
            ->setEmail( 'admin@randycar.fr' )
            ->setPhone( '00000000' )
            ->setPassword($hashedPassword)
            ->setAvatar('')
            ->setBio( 'Le staff RandyCar' )
            ->setCreatedAt( \DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
            ->setTripCount(0)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($userAdminEntity);
        array_push($userArray, $userAdminEntity);

        for($i=0; $i <= 10; $i++){
            $userEntity = new User();

            $phoneNumber = '0';
            $phoneNumber .= $faker->numberBetween(1,6);
            $phoneNumber .= $faker->regexify('[1-5]{6}');

            $plaintextPassword = $faker->password();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $userEntity,
                $plaintextPassword
            );

            $userEntity->setName( $faker->name() )
                ->setSurname( $faker->lastName() )
                ->setEmail( $faker->email() )
                ->setPhone( $phoneNumber )
                ->setPassword( $hashedPassword )
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

        for($j=0; $j <= 80; $j++){
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

        //Mail 
        $mailArray = [];

        for($t=0; $t <= 150; $t++) { 
            $mailEntity = new Mail();
            $mailEntity->setReceiver($userArray[random_int(0, count($userArray)-1)])
                ->setObject($faker->word())
                ->setContent($faker->sentence(10))
                ->setSentDate(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ));
            
            array_push($mailArray, $mailEntity);
            $manager->persist($mailEntity);
        }

        //City 
        $cityArray = [];

        for($l =0; $l<=50; $l++) { 
            $cityEntity = new City();
            $cityEntity->setPostalCode(str_replace(' ', '',$faker->postcode()))
                ->setName($faker->city());

            array_push($cityArray, $cityEntity);
            $manager->persist($cityEntity);
        }

        //Address
        $addressArray = [];

        for($k=0;$k<=80;$k++) { 
            $addressEntity = new Address();

            $addressEntity->setNumber($faker->buildingNumber())
                ->setStreet($faker->streetName())
                ->setCity(  $cityArray[random_int(0, count($cityArray) - 1 )]);
            
            array_push($addressArray, $addressEntity);
            $manager->persist($addressEntity);
        }

        //Messages 
        $messageArray = [];

        for($m=0;$m<=100;$m++) { 
            $messageEntity = new Messages();
            $messageEntity->setCreatedAt(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
                ->setContent($faker->realText())
                ->setAuthor($userArray[random_int(0, count($userArray) - 1 )])
                ->setReceiver($userArray[random_int(0, count($userArray) - 1 )])
                ->setIsRead($faker->dateTimeBetween('-200 days', 'now'));

            array_push($messageArray, $messageEntity);
            $manager->persist($messageEntity);
        }

        // //Notifications 
        $notificationArray = [];

        for($n = 0; $n<100; $n++) { 
            $notificationEntity = new Notification();

            $notificationEntity
                ->setReceiver($userArray[random_int(0, count($userArray) - 1 )])
                ->setObject($faker->word())
                ->setContent($faker->word())
                ->setCreatedAt($faker->dateTimeBetween("-200 days", "now") )
                ->setReaded($faker->boolean());

            array_push($notificationArray, $notificationEntity);
            $manager->persist($notificationEntity);
        }

        //Opinions
        $opinionArray = [];
        for($z=0;$z<200;$z++){
            $opinionEntity = new Opinion();
            $opinionEntity->setNotation($faker->biasedNumberBetween(0,5))
                ->setMessage($faker->word())
                ->setCreatedAt(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween("-200 days", "now") ))
                ->setEmitter($userArray[random_int(0, count($userArray) - 1 )])
                ->setReceptor($userArray[random_int(0, count($userArray) - 1 )]);

                array_push($opinionArray, $opinionEntity);
                $manager->persist($opinionEntity);
        }

        //Trips
        $tripArray = [];
        for($t = 0 ; $t< 100; $t++) { 
            $tripEntity = new Trip();
            $tripEntity->setMaxPassenger($faker->biasedNumberBetween(1,5))
                ->setDateStart($faker->dateTimeBetween('-200 days', 'now'))
                ->setIsFinished($faker->boolean())
                ->setIsCancelled($faker->boolean())
                ->setDriver($userArray[random_int(0, count($userArray) - 1 )])
                ->setStartAddress($addressArray[random_int(0, count($addressArray) - 1 )])
                ->setDestinationAddress($addressArray[random_int(0, count($addressArray) - 1 )])
                ->addPassenger($userArray[random_int(0, count($userArray) - 1 )]);

            array_push($tripArray, $tripEntity);
            $manager->persist($tripEntity);
        }

        $manager->flush();

    }
}
