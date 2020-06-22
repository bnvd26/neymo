<?php

namespace App\Controller\SuperAdmin;

use App\Entity\GovernanceUserInformation;
use App\Form\GovernanceUserInformationType;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/superadmin/governance/user")
 * 
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class GovernanceUserInformationController extends AbstractController
{
    /**
     * @Route("/index", name="governance_user_information_index", methods={"GET"})
     */
    public function index(GovernanceUserInformationRepository $governanceUserInformationRepository): Response
    {
        return $this->render('superAdmin/governanceUserInformation/index.html.twig', [
            'governance_user_informations' => $governanceUserInformationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="governance_user_information_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $governanceUserInformation = new GovernanceUserInformation();
        $form = $this->createForm(GovernanceUserInformationType::class, $governanceUserInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($governanceUserInformation);
            $entityManager->flush();

            return $this->redirectToRoute('governance_user_information_index');
        }

        return $this->render('superAdmin/governanceUserInformation/new.html.twig', [
            'governance_user_information' => $governanceUserInformation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governance_user_information_show", methods={"GET"})
     */
    public function show(GovernanceUserInformation $governanceUserInformation): Response
    {
        return $this->render('superAdmin/governanceUserInformation/show.html.twig', [
            'governance_user_information' => $governanceUserInformation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="governance_user_information_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GovernanceUserInformation $governanceUserInformation): Response
    {
        $form = $this->createForm(GovernanceUserInformationType::class, $governanceUserInformation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('governance_user_information_index');
        }

        return $this->render('superAdmin/governanceUserInformation/edit.html.twig', [
            'governance_user_information' => $governanceUserInformation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governance_user_information_delete", methods={"DELETE"})
     */
    public function delete(Request $request, GovernanceUserInformation $governanceUserInformation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$governanceUserInformation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governanceUserInformation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('governance_user_information_index');
    }
}
