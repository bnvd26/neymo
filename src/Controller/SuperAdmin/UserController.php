<?php

namespace App\Controller\SuperAdmin;

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
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/superadmin/user")
 *
 * @IsGranted("ROLE_SUPER_ADMIN")
*/
class UserController extends AbstractController
{
    /**
     * @Route("/create", name="superadmin_user_create")
     */
    public function create(Request $request, GovernanceRepository $govRepository)
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('governance', ChoiceType::class, [
                'choices' => [
                    'Gouvernances' => $govRepository->findAll(),
                 ]])
            ->add('save', SubmitType::class, ['label' => 'Create user'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('superadmin_user_index');
        }

        return $this->render('superAdmin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/index", name="superadmin_user_index", methods={"GET"})
     */
    public function index(GovernanceUserInformationRepository $governanceUserInformationRepository): Response
    {
        return $this->render('superAdmin/governanceUserInformation/index.html.twig', [
            'governance_user_informations' => $governanceUserInformationRepository->findAll(),
        ]);
    }
}
