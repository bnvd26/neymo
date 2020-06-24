<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ParticularController extends AbstractController
{
    /**
     * @Route("/admin/particular", name="admin_particular")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $particulars = $this->getGovernanceCurrentUser()->getGovernance()->getParticulars();

        return $this->render('admin/particular/index.html.twig', compact('particulars'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }
}
