<?php

namespace App\Controller\Admin;

use App\Entity\Governance;
use App\Entity\GovernanceUserInformation;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\GovernanceRepository;
use App\Repository\GovernanceUserInformationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GovernanceUserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     *
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/admin/users/create", name="admin_users_create")
     *
     * @IsGranted("ROLE_ADMIN")
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
            $this->addFlash('success', 'L\'utilisateur a bien été créer');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/governanceUsers/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
