<?php

namespace App\Controller\API;

use App\Repository\GovernanceRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\API\ApiController;

class GovernanceController extends ApiController
{
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
        }

        return $this->responseOk($governanceArray);
    }
}
