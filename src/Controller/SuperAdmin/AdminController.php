<?php

namespace App\Controller\SuperAdmin;

use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use App\Form\AdminSuperAdminType;
use App\Repository\GovernanceRepository;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/superadmin/admin", name="superadmin_admin_")
 *
 * @IsGranted("ROLE_SUPER_ADMIN")
*/
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function list(GovernanceUserInformationRepository $governanceUserInformationRepository)
    {
        return $this->render('superAdmin/admin/index.html.twig', [
            'admins' => $governanceUserInformationRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/create/", name="create")
     */
    public function create(Request $request, GovernanceRepository $govRepository, $id, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer un administrateur', 'attr' => ['class' => 'btn btn-success']])
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $this->createUserInformation($request, $user, $govRepository->find($id));
            $entityManager->flush();

            return $this->redirectToRoute('superadmin_governance_show', ['id' => $id]);
        }

        return $this->render('superAdmin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(GovernanceUserInformation::class);
        /* @var GovernanceUserInformation $user */
        $user = $repo->find($id);
        $form = $this->createForm(AdminSuperAdminType::class, $user);
        $form->get('email')->setData($user->getUser()->getEmail());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->getUser()->setEmail($form->get("email")->getData());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('superadmin_governance_show', ['id' => $user->getGovernance()->getId()]);
        }

        return $this->render('superAdmin/admin/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, int $id): Response
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(GovernanceUserInformation::class);
        /* @var GovernanceUserInformation $user */
        $user = $repo->find($id);
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('superadmin_governance_show', ['id' => $user->getGovernance()->getId()]);
    }

    public function createUserInformation($request, $user, $governance)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $govUserInformation = new GovernanceUserInformation();
        $govUserInformation->setFirstName($request->request->get('firstName'));
        $govUserInformation->setLastName($request->request->get('lastName'));
        $govUserInformation->setRole($request->request->get('role'));
        $govUserInformation->setUser($user);
        $govUserInformation->setGovernance($governance);
        $entityManager->persist($govUserInformation);
    }
}
