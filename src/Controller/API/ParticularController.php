<?php

namespace App\Controller\API;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticularController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/particular/update", name="api_particular_update", methods="PUT")
     */
    public function update(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo)
    {
        // User information
        $user = $userRepo->find($this->getUser()->getId());

        $password = $passwordEncoder->encodePassword($user, $user->getPassword());

        $user->setPassword($password);

        $user->getParticular()->setFirstName('Money');

        $this->em->persist($user);

        $this->em->flush();

        return $this->responseOk([
            'Success' => "L'utilisateur a bien été modifier",
        ]);
    }

    /**
     * @Route("/api/particular/account", name="api_particular_account", methods="GET")
     */
    public function accountState()
    {
        if (!$this->getUser()->isParticular()) {
            return $this->responseOk([
                'Information' => "Il n y a pas de compte particulier pour cet utilisateur",
                ]);
        };

        return $this->responseOk([
            'account_id' => $this->getUser()->getParticular()->getAccount()->getId(),
            'available_cash' => $this->getUser()->getParticular()->getAccount()->getAvailableCash()
            ]);
    }
}
