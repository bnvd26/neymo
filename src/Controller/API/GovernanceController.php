<?php

namespace App\Controller\API;

use App\Repository\GovernanceRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\API\ApiController;
use Swagger\Annotations as SWG;

class GovernanceController extends ApiController
{
    /**
     * @Route("/api/governances", name="api_governances", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Governances listed"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Tag(name="governances")
     *
     * @param GovernanceRepository $governanceRepository
     *
     * @return Response
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
