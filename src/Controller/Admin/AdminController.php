<?php

namespace App\Controller\Admin;

use App\Entity\GovernanceUserInformation;
use App\Form\AdminType;
use App\Repository\CompanyRepository;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin", name="admin_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="home")
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

    /**
     * @Route("/profile/show", name="profile_show")
     */
    public function show(Request $request, GovernanceUserInformationRepository $governanceUserInformationRepository)
    {
        $profile = $governanceUserInformationRepository->findOneBy(['user' => $this->getUser()->getId()]);

        return $this->render('admin/profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit", methods={"GET","POST"})
     */
    public function edit(Request $request)
    {
        $user = $this->getUser()->getGovernanceUserInformation();
        
        $form = $this->createForm(AdminType::class, $user);
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
           
            $this->getUser()->setEmail($request->request->get('email'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_profile_show');
        }

        return $this->render('admin/profile/edit.html.twig', [
           'user' => $this->getUser(),
           'form' => $form->createView(),
       ]);
    }
}
