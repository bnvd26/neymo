<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class UserController extends ApiController
{
    /**
     * @Route("/api/me", name="api_me", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Show my details"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="me")
     */
    public function getCurrentUser()
    {
        $isParticular = $this->getUser()->isParticular();

        $user = [
            'id' => $this->getUser()->getId(),
            'mail' => $this->getUser()->getEmail(),
            'type' => $isParticular ? 'particular' : 'company',
            'first_name' => $isParticular ? $this->getUser()->getParticular()->getFirstName() : $this->getUser()->getCompany()->getFirstName(),
            'last_name' => $isParticular ? $this->getUser()->getParticular()->getLastName() : $this->getUser()->getCompany()->getLastName(),
            'address' => $isParticular ? $this->getUser()->getParticular()->getAddress() : $this->getUser()->getCompany()->getAddress(),
            'number_phone' => $isParticular ? $this->getUser()->getParticular()->getPhoneNumber() : $this->getUser()->getCompany()->getPhoneNumber(),
            'postal_code' => $isParticular ? $this->getUser()->getParticular()->getZipCode() : $this->getUser()->getCompany()->getZipCode(),
            'city' => $isParticular ? $this->getUser()->getParticular()->getCity() : $this->getUser()->getCompany()->getCity(),
            'siret' => $isParticular ? null : $this->getUser()->getCompany()->getSiret(),
            'name' => $isParticular ? null : $this->getUser()->getCompany()->getName(),
            'description' => $isParticular ? null : $this->getUser()->getCompany()->getDescription(),
            'category' => $isParticular ? null : $this->getUser()->getCompany()->getCategory()->getName(),
            'governance' => $isParticular ? $this->getUser()->getParticular()->getGovernance()->getName() : $this->getUser()->getCompany()->getGovernance()->getName() 
        ];

        return $this->responseOk($user);
    }
}
