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
                'first_name' => $this->getUser()->getCompany()->getFirstName()
            ];
        }

        if ($this->getUser()->isParticular()) {
            $user = $this->getUser();
            $user = [
                'id' => $user->getId(),
                'type' => 'particular',
                'first_name' => $user->getParticular()->getFirstName()
            ];
        }

        return $this->responseOk($user);
    }
}
