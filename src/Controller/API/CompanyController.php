<?php

namespace App\Controller\API;

use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\API\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompanyController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/company/update", name="api_company_update", methods="PUT")
     */
    public function update(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepo): response
    {
        // User information
        $user = $userRepo->find($this->getUser()->getId());

        $password = $passwordEncoder->encodePassword($user, $user->getPassword());

        $user->setPassword($password);

        $user->getCompany()->setSiret('je suis modifier');

        $this->em->persist($user);

        $this->em->flush();

        return $this->responseCreated(([
            'Success' => "L'utilisateur a bien été modifier",
        ]));
    }

    /**
     * @Route("/api/companies", name="api_company_list", methods="GET")
     */
    public function getListCompanies(CompanyRepository $companyRepository)
    {
        $companies = $companyRepository->findCompanyValidatedByGovernance($this->getUser()->getUserGovernanceId());

        $companyArray = [];

        foreach ($companies as $company) {
            $companyArray[] = [
                'id' => $company->getId(),
                'company_name' => $company->getName(),
                'account_number' => $company->getAccount()->getAccountNumber(),
                'first_name' => $company->getFirstName(),
                'category' => $company->getCategory()->getName(),
                'governance' => $company->getGovernance()->getName()
            ];
        }

        return $this->responseOk($companyArray);
    }
}
