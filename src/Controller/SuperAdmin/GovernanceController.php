<?php

namespace App\Controller\SuperAdmin;

use App\Entity\Governance;
use App\Form\GovernanceType;
use App\Repository\GovernanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/superadmin/governance")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class GovernanceController extends AbstractController
{
    /**
     * @Route("/index", name="governance_index", methods={"GET"})
     */
    public function index(GovernanceRepository $governanceRepository): Response
    {
        return $this->render('superAdmin/governance/index.html.twig', [
            'governances' => $governanceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="governance_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $governance = new Governance();
        $form = $this->createForm(GovernanceType::class, $governance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($governance);
            $entityManager->flush();

            return $this->redirectToRoute('superAdmin/governance_index');
        }

        return $this->render('superAdmin/governance/new.html.twig', [
            'governance' => $governance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governance_show", methods={"GET"})
     */
    public function show(Governance $governance): Response
    {
        return $this->render('superAdmin/governance/show.html.twig', [
            'governance' => $governance,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="governance_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Governance $governance): Response
    {
        $form = $this->createForm(GovernanceType::class, $governance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('governance_index');
        }

        return $this->render('superAdmin/governance/edit.html.twig', [
            'governance' => $governance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governance_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Governance $governance): Response
    {
        if ($this->isCsrfTokenValid('delete'.$governance->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('superAdmin/governance_index');
    }
}
