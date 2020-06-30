<?php

namespace App\Controller\Admin;

use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users", name="admin_users_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class GovernanceUserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(GovernanceUserInformationRepository $governanceUserInformationRepository)
    {
        $governanceId = $this->getGovernanceCurrentUser()->getGovernance()->getId();

        $governanceUserInformation = $governanceUserInformationRepository->findUserInformationByGovernanceId($governanceId);

        return $this->render('admin/governanceUsers/index.html.twig', compact('governanceUserInformation'));
    }

    public function getGovernanceCurrentUser()
    {
        return $this->getUser()->getGovernanceUserInformation();
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();

        $user->setRoles(["ROLE_ADMIN"]);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $govUserInformation = new GovernanceUserInformation();
            $govUserInformation->setFirstName($request->request->get('firstName'));
            $govUserInformation->setLastName($request->request->get('lastName'));
            $govUserInformation->setRole($request->request->get('role'));
            $govUserInformation->setUser($user);
            $govUserInformation->setGovernance($this->getGovernanceCurrentUser()->getGovernance());
            $entityManager->persist($govUserInformation);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été créé');
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/governanceUsers/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, GovernanceUserInformation $GovernanceUserInformation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$GovernanceUserInformation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($GovernanceUserInformation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_users_index');
    }
}
