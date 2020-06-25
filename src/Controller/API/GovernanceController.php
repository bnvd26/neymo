<?php

namespace App\Controller\API;

use App\Repository\GovernanceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GovernanceController extends AbstractController
{
    private function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @Route("/api/governances", name="api_governances", methods="GET")
     */
    public function getAllGovernances(GovernanceRepository $governanceRepository)
    {
        $governances = $governanceRepository->findAll();

        $governanceArray = [];
        
        foreach ($governances as $governance) {
            
            $governanceArray[] = [
                'id' => $governance->getId(),
                'governance_name' => $governance->getName()
            ];

            $json = $this->serialize($governanceArray);
           
        }

        $response = new Response($json, 200);

        return $response;
    }
}
