<?php

namespace App\Tests;

class Utils{

    public static function getRandomIdByCollections($class, $entityManager)
        {
            $collections =  $entityManager->getRepository($class)->findAll();
            $random = $collections[random_int(0, count($collections) - 1 )];
            return $random->getId();
        }  

}
