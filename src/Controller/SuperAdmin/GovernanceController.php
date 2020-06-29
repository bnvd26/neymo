<?php

namespace App\Controller\SuperAdmin;

use App\Entity\Governance;
use App\Form\GovernanceType;
use App\Repository\GovernanceRepository;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/superadmin/governance", name="superadmin_governance_")
 *
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class GovernanceController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"GET","POST"})
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

            return $this->redirectToRoute('superadmin_home');
        }

        return $this->render('superAdmin/governance/create.html.twig', [
            'governance' => $governance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Governance $governance, GovernanceUserInformationRepository $govUserInfoRepository): Response
    {
        $govUsersInformation = $govUserInfoRepository->findUserInformationByGovernanceId($governance->getId());

        return $this->render('superAdmin/governance/show.html.twig', [
            'govUsersInformation' => $govUsersInformation,
            'governance' => $governance
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Governance $governance): Response
    {
        $form = $this->createForm(GovernanceType::class, $governance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('governance_show', ['id' => $governance->getId()]);
        }

        return $this->render('superAdmin/governance/edit.html.twig', [
            'governance' => $governance,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display list of Governances.
     *
     * @Route("/", name="index")
     *
     * @param [type] $governanceRepository
     *
     * @return Response
     */
    public function index(GovernanceRepository $governanceRepository)
    {
        return $this->render('superAdmin/home.html.twig', [
            'governances' => $governanceRepository->findAll()
        ]);
    }
}
