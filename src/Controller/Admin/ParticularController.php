<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Form\ParticularAdminType;
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
            $particular->setValidated(true);
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
            $this->addFlash('success', 'Le particulier a bien été créé');
            return $this->redirectToRoute('admin_particular');
        }

        return $this->render('admin/particular/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_particular_edit", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Particular $particular): Response
    {
        $form = $this->createForm(ParticularAdminType::class, $particular);
        $form->get('email')->setData($particular->getUser()->getEmail());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $particular->getUser()->setEmail($form->get("email")->getData());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_particular');
        }

        return $this->render('admin/particular/edit.html.twig', [
            'company' => $particular,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_particular_delete", methods={"DELETE"})
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Particular $particular): Response
    {
        if ($this->isCsrfTokenValid('delete'.$particular->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($particular);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_particular');
    }
}
