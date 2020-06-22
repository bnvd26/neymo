<?php

namespace App\Controller\SuperAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SuperAdminController extends AbstractController
{
    /**
     * @Route("/superadmin/home", name="superadmin_home")
     * 
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function home()
    {
        return $this->render('superAdmin/dashboard.html.twig');
    }
}
