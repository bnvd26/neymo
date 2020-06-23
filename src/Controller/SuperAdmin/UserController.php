<?php

namespace App\Controller\SuperAdmin;

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
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/superadmin/governance")
 *
 * @IsGranted("ROLE_SUPER_ADMIN")
*/
class UserController extends AbstractController
{
    /**
     * @Route("/{id}/create/", name="superadmin_user_create")
     */
    public function create(Request $request, GovernanceRepository $govRepository, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $form = $this->createFormBuilder($user)
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create user'])
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setGovernance($govRepository->find($id));
            $entityManager->persist($user);
            $this->createUserInformation($request, $user);
            $entityManager->flush();

            return $this->redirectToRoute('governance_show', ['id' => $id]);
        }

        return $this->render('superAdmin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function createUserInformation($request, $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $govUserInformation = new GovernanceUserInformation();
        $govUserInformation->setFirstName($request->request->get('firstName'));
        $govUserInformation->setLastName($request->request->get('lastName'));
        $govUserInformation->setRole($request->request->get('role'));
        $govUserInformation->setUser($user);
        $entityManager->persist($govUserInformation);
    }
}
