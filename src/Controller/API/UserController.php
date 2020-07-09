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
        if ($this->getUser()->isCompany()) {
            $user = [
                'id' => $this->getUser()->getCompany()->getId(),
                'type' => 'company',
                'first_name' => $this->getUser()->getCompany()->getFirstName(),
                'last_name' => $this->getUser()->getCompany()->getLastName(),
                'address' => $this->getUser()->getCompany()->getAddress(),
                'number_phone' => $this->getUser()->getCompany()->getPhonenumber(),
                'name' => $this->getUser()->getCompany()->getName(),
                'siret' => $this->getUser()->getCompany()->getSiret(),
                'category' => $this->getUser()->getCompany()->getCategory()->getName(),
                'description' => $this->getUser()->getCompany()->getDescription(),
                'postal_code' => $this->getUser()->getComapny()->getZipCode(),
                'city' => $this->getUser()->getComapny()->getCity(),
                
            ];
        }

        if ($this->getUser()->isParticular()) {
            $user = [
                'id' => $this->getUser()->getId(),
                'type' => 'particular',
                'first_name' => $this->getUser()->getParticular()->getFirstName(),
                'last_name' => $this->getUser()->getParticular()->getLastName(),
                'address' => $this->getUser()->getParticular()->getAddress(),
                'number_phone' => $this->getUser()->getParticular()->getPhoneNumber(),
                'postal_code' => $this->getUser()->getParticular()->getZipCode(),
                'city' => $this->getUser()->getParticular()->getCity(),
            ];
        }

        return $this->responseOk($user);
    }
}
