<?php

namespace App\Controller\Admin;

use App\Repository\GovernanceRepository;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GovernanceUserController extends AbstractController
{
    /**
     * @Route("/admin/governance-user", name="admin_governance_user")
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(GovernanceUserInformationRepository $repository)
    {
        $governanceUser = $repository->findAll();

        return $this->render('admin/governanceUser/list.html.twig', compact('governanceUser'));
    }
}
