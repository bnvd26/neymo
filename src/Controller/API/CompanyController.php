<?php

namespace App\Controller\API\CompanyController;

use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompanyController extends AbstractController 
{
    /**
     * @Route("/api/company/update", name="api_company_update", methods="PUT")
     */
    public function update(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser()->getId();

        // User information
        $user = $userRepo->find($user);

        $password = $passwordEncoder->encodePassword($user, $user->getPassword());

        $user->setPassword($password);

        $entityManager->persist($user);

        foreach($user->getCompanies() as $user) {
            $user->setSiret('Je suis modifié');
        }

        $entityManager->persist($user);

        $entityManager->flush();

        $response = new Response();

        $response->setStatusCode(Response::HTTP_CREATED);

        $response->setContent(json_encode([
            'Success' => "L'utilisateur a bien été modifier",
        ]));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;

        return $user;
    }
}
