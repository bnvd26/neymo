<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    ///////////////
    // Responses
    //////////////

    protected function responseOk($obj)
    {
        $response = (new Response())

        ->setStatusCode(Response::HTTP_OK)

        ->setContent(json_encode($obj));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    protected function responseNotAllowed($msg)
    {
        $response = (new Response())
        
        ->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)
        
        ->setContent(json_encode($msg));
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    protected function responseCreated($msg)
    {
        $response = (new Response())

        ->setStatusCode(Response::HTTP_CREATED)

        ->setContent(json_encode($msg));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    ///////////////
    // Serialize
    //////////////

    protected function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    protected function deserialize($data, $entity)
    {
        return $this->container->get('serializer')->deserialize($data, $entity, 'json');
    }
}
