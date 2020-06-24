<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/admin/company/create", name="admin_company_create")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $company = new Company();

        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            $company->setGovernance($this->getGovernanceCurrentUser()->getGovernance());
            $entityManager->persist($company);
            $entityManager->flush();
            $this->addFlash('success', 'Le commerçant a bien été créer');
            return $this->redirectToRoute('admin_company');
        }

        return $this->render('admin/company/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
