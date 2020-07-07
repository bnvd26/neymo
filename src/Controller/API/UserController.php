<?php

namespace App\Controller\API;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends ApiController
{
    /**
     * @Route("/api/me", name="api_me", methods="GET")
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
                'siret' => $this->getUser()->getCompany()->getSiret(),
                'category' => $this->getUser()->getCompany()->getCategory()->getName(),
                'description' => $this->getUser()->getCompany()->getDescription()
                
            ];
        }

        if ($this->getUser()->isParticular()) {
            $user = [
                'id' => $this->getUser()->getId(),
                'type' => 'particular',
                'first_name' => $this->getUser()->getParticular()->getFirstName(),
                'last_name' => $this->getUser()->getParticular()->getLastName(),
                'address' => $this->getUser()->getParticular()->getAddress(),
                'number_phone' => $this->getUser()->getParticular()->getPhoneNumber()
            ];
        }

        return $this->responseOk($user);
    }
}
