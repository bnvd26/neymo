<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CompanyController extends AbstractController
{
    /**
     * @Route("/admin/company", name="admin_company")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $companies = $this->getGovernanceCurrentUser()->getGovernance()->getCompanies();

        return $this->render('admin/company/index.html.twig', compact('companies'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }
}
