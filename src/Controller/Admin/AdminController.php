<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function home()
    {
        $user = $this->getGovernanceCurrentUser();

        return $this->render('admin/home.html.twig', compact('user'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }
}
