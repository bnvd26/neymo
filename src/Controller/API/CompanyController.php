<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\GovernanceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompanyController extends AbstractController
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

        foreach ($user->getCompanies() as $user) {
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
    }

    /**
     * @Route("/api/company/account", name="api_company_account", methods="GET")
     */
    public function accountState()
    {
        $companyArray = [];

        foreach ($this->getUser()->getCompanies() as $company) {
            $companyArray[] = [
                'account_id' => $company->getAccount()->getId(),
                'available_cash' => $company->getAccount()->getAvailableCash(),
            ];
        }

        if (!$this->verifyCompanyAccountExist($companyArray)) {
            $response = new Response();

            $response->setStatusCode(Response::HTTP_OK);

            $response->setContent(json_encode([
            'Information' => "Il n y a pas de compte professionel pour cet utilisateur",
            ]));

            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }

        $response = new JsonResponse($companyArray);

        $response->setStatusCode(Response::HTTP_OK);
    
        $response->headers->set('Content-Type', 'application/json');
            
        return $response;
    }


    /**
     * Undocumented function
     *
     * @param [type] $companyArray
     * @return bool
     */
    public function verifyCompanyAccountExist($companyArray)
    {
        return !empty($companyArray);
    }

    /**
     * @Route("/api/companies", name="api_company_account", methods="GET")
     */
    public function getListCompanies(CompanyRepository $companyRepository)
    {
        $currentUser = $this->getUser();

        if ($currentUser->isParticular()) {
            $governanceId = $currentUser->getParticular()->getGovernance()->getId();
        } elseif ($currentUser->isCompany()) {
            foreach ($currentUser->getCompanies() as $company) {
                $governanceId = $company->getGovernance()->getId();
            }
        }

        $companies = $companyRepository->findCompanyValidatedByGovernance($governanceId);

        $companyArray = [];

        foreach ($companies as $company) {
            $companyArray[] = [
                'id' => $company->getId(),
                'company_name' => $company->getName(),
                'first_name' => $company->getFirstName()
            ];
        }

        $json = $this->serialize($companyArray);

        $response = new Response($json, 200);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
