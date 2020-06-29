<?php

namespace App\Controller\API;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Repository\GovernanceRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    private function deserialize($data, $entity)
    {
        return $this->container->get('serializer')->deserialize($data, $entity, 'json');
    }

    /**
     * @Route("/api/register", name="api_register", methods="POST")
     */
    public function register(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, GovernanceRepository $governanceRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->deserialize($request->getContent(), User::class);

        if ($this->emailExist($userRepository, $user->getEmail())) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
            'Error' => "Un utilisateur existe déjà avec cette adresse mail",
            ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }
        
        $password = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(["ROLE_USER"]);
        $dataDecoded = json_decode($request->getContent());
        $entityManager->persist($user);
        $entityManager->flush();
        $dataDecoded->type == "particular" ? $this->createParticular($request, $user, $governanceRepository) : $this->createCompany($request, $user, $governanceRepository);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setContent(json_encode([
            'Success' => "L'utilisateur a bien été enregistrer",
        ]));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    public function createParticular(Request $request, $user, $governanceRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dataDecoded = json_decode($request->getContent());
        $particular = new Particular();
        $particular->setGovernance($governanceRepository->find($dataDecoded->governanceId));
        $particular->setFirstName($dataDecoded->firstName);
        $particular->setLastName($dataDecoded->lastName);
        $particular->setZipCode($dataDecoded->zipCode);
        $particular->setCity($dataDecoded->city);
        $particular->setAddress($dataDecoded->address);
        $particular->setPhoneNumber($dataDecoded->phoneNumber);
        $particular->setUser($user);
        $particular->setBirthdate(new \DateTime($dataDecoded->birthdate));
        $particular->setValidated(false);
        $entityManager->persist($particular);
        $entityManager->flush();
    }

    public function createCompany(Request $request, $user, $governanceRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $dataDecoded = json_decode($request->getContent());
        $company = new Company();
        $company->setGovernance($governanceRepository->find($dataDecoded->governanceId));
        $company->setFirstName($dataDecoded->firstName);
        $company->setName($dataDecoded->name);
        $company->setLastName($dataDecoded->lastName);
        $company->setZipCode($dataDecoded->zipCode);
        $company->setCity($dataDecoded->city);
        $company->setAddress($dataDecoded->address);
        $company->setPhoneNumber($dataDecoded->phoneNumber);
        $company->addUser($user);
        $company->setValidated(false);
        $company->setSiret($dataDecoded->siret);
        $entityManager->persist($company);
        $entityManager->flush();
    }

    public function emailExist($userRepository, $email)
    {
        return $userRepository->findBy(['email' => $email]);
    }
}
