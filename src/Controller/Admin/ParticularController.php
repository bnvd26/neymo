<?php

namespace App\Controller\Admin;

use App\Entity\Particular;
use App\Entity\User;
use App\Form\ParticularType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/admin/particular/create", name="admin_particular_create")
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $particular = new Particular();

        $form = $this->createForm(ParticularType::class, $particular);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $particular = $form->getData();
            $particular->setGovernance($this->getGovernanceCurrentUser()->getGovernance());
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $particular->setUser($user);
            $entityManager->persist($particular);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Le particulier a bien été créer');
            return $this->redirectToRoute('admin_particular');
        }

        return $this->render('admin/particular/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
