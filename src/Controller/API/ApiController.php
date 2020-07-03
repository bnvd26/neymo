<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    ///////////////
    // Responses
    //////////////

    public function responseOk($obj)
    {
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);

        $response->setContent(json_encode($obj));

        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    public function responseNotAllowed($msg)
    {
        $response = new Response();
        
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        
        $response->setContent(json_encode($msg));
        
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    public function responseCreated($msg)
    {
        $response = new Response();

        $response->setStatusCode(Response::HTTP_CREATED);

        $response->setContent(json_encode($msg));

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
