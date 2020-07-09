<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\User;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
     * @Route("/admin/company", name="admin_company_")
     *
     * @IsGranted("ROLE_ADMIN")
     */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(CompanyRepository $companyRepository)
    {
        $currentGovernance = $this->getGovernanceCurrentUser()->getGovernance();

        $companies = $companyRepository->findCompanyValidatedByGovernance($currentGovernance);

        return $this->render('admin/company/index.html.twig', compact('companies'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $company = new Company();

        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $company = $form->getData();
            $company->setValidated(true);
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
            $this->addFlash('success', 'Le commerçant a bien été créé');

            return $this->redirectToRoute('admin_company_index');
        }

        return $this->render('admin/company/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Company $company): Response
    {
        /*        $users = $company->getUser();
                if ([] === $users) {
                    // @todo erreur, user is not found
                }
                $user = current($users);
                if (!$user instanceof User) {
                    // @todo erreur, user is not a user
                }*/
        $form = $this->createForm(CompanyType::class, $company);
        // $form->get('email')->setData($user->getEmail());
        $form->get('email')->setData($company->getUser()[0]->getEmail());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$user->setEmail($form->get("email")->getData());
            $company->getUser()[0]->setEmail($form->get("email")->getData());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le commerçant a bien été modifié');

            return $this->redirectToRoute('admin_company_index');
        }

        return $this->render('admin/company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show($id, CompanyRepository $companyRepo)
    {
        $company = $companyRepo->find($id);

        return $this->render('admin/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Company $company): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($company);
            $entityManager->flush();
            $this->addFlash('success', 'Le commerçant a bien été supprimé');
        }

        return $this->redirectToRoute('admin_company_index');
    }
}
