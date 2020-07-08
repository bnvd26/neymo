<?php

namespace App\Controller\SuperAdmin;

use App\Repository\GovernanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SuperAdminController extends AbstractController
{
    /**
     * @Route("/superadmin/home", name="superadmin_home")
     *
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function home(GovernanceController $governanceController, GovernanceRepository $governanceRepository )
    {
        return $this->render('superAdmin/home.html.twig', [
            'governances' => $governanceRepository->findAll()
        ]);
    }

    
}
