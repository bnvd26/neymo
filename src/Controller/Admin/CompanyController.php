<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\User;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
     * @Route("/admin/company")
     *
     * @IsGranted("ROLE_ADMIN")
     */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="admin_company")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(CompanyRepository $companyRepository)
    {
        $currentGovernance = $this->getGovernanceCurrentUser()->getGovernance();

        // $companyRepository->findCompanyValidatedByGovernance(1, $currentGovernance);
        
        $companies = $currentGovernance->getCompanies();

        return $this->render('admin/company/index.html.twig', compact('companies'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }

    /**
     * @Route("/create", name="admin_company_create")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $company = new Company();

        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            $company->setGovernance($this->getGovernanceCurrentUser()->getGovernance());
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $company->addUser($user);
            $user->addCompany($company);
            $entityManager->persist($company);
            $entityManager->flush();
            $this->addFlash('success', 'Le commerçant a bien été créer');
            return $this->redirectToRoute('admin_company');
        }

        return $this->render('admin/company/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_company_show")
     */
    public function show($id, CompanyRepository $companyRepo)
    {
        $company = $companyRepo->find($id);
        
        return $this->render('admin/company/show.html.twig', [
            'company' => $company,
        ]);
    }
}
