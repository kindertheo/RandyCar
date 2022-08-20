<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class PingController extends AbstractController
{
    /**
     * @Route("/ping", name="ping")
     */
    public function index(): Response
    {
        return $this->json(['message' => 'ping OK']);
    }

    /**
     * @Route("/test_auth", name="test_auth")
     * @IsGranted("ROLE_USER")
     */
    public function test_auth(): Response
    {
        return $this->json(['message' => 'auth OK']);
    }

}
