<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/api/me", name="api_me")
     */
    public function getCurrentUser()
    {
        $user = $this->getUser();

        $json = $this->serialize($user);

        $response = new Response($json, 200);

        return $response;
    }
}
