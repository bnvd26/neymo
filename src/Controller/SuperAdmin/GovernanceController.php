<?php

namespace App\Controller\SuperAdmin;

use App\Repository\GovernanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GovernanceController extends AbstractController
{
    /**
     * @Route("/superadmin/governance", name="superadmin_governance")
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(GovernanceRepository $repository)
    {
        $governances = $repository->findAll();

        return $this->render('superAdmin/governance/list.html.twig', compact('governances'));
    }
}
