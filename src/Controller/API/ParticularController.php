<?php

namespace App\Controller\API;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Particular edited"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="particular")
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepo
     *
     * @return object|Response
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
}
