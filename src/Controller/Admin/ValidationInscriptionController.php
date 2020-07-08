<?php

namespace App\Controller\Admin;

use App\Entity\Account;
use App\Entity\Directory;
use App\Entity\Particular;
use App\Entity\User;
use App\Form\ParticularAdminType;
use App\Form\ParticularType;
use App\Repository\CompanyRepository;
use App\Repository\ParticularRepository;
use App\Repository\UserRepository;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/validation-inscription", name="admin_validation_inscription_")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ValidationInscriptionController extends AbstractController
{
    /**
     * @Route("/company", name="company_index")
     */
    public function companyList(CompanyRepository $companyRepository)
    {
        $governanceId = $this->getUser()->getGovernanceUserInformation()->getGovernance()->getId();

        $companies = $companyRepository->findCompanyNotValidatedByGovernance($governanceId);

        return $this->render('admin/inscriptionValidation/company/index.html.twig', compact('companies'));
    }

    /**
     * @Route("/company/{id}/validate", name="company_validate")
     */
    public function companyValidate ($id, CompanyRepository $companyRepository, MailerInterface $mailer) 
    {
        $em = $this->getDoctrine()->getManager();
        $company = $companyRepository->find($id);

        $company->setValidated(true);
        $account = new Account();
        $account->setAccountNumber(rand(1, 100000));
        $account->setAvailableCash(0);
        $company->setAccount($account);
        $em->persist($account);
        $directory = new Directory();
        $directory->setAccount($account);
        $em->persist($directory);
        $em->flush();

        $userEmail = null;

        foreach($company->getUser() as $user) {
            $userEmail = $user->getEmail();
        }

        $this->sendEmailValidation();

        $this->addFlash('success', 'L\'utilisateur a bien été validé');
        return $this->redirectToRoute('admin_validation_inscription_company_index');
    }

    /**
     * @Route("/company/{id}/no-validate", name="company_no_validate")
     *
     * @param [type] $id
     * @return void
     */
    public function companyNoValidate($id, CompanyRepository $companyRepository, UserRepository $userRepository, MailerInterface $mailer) {
        $em = $this->getDoctrine()->getManager();
        $company = $companyRepository->find($id);
        $userId = null;
        $userEmail = null;
        
        foreach($company->getUser() as $user) {
            $userId = $user->getId();
            $userEmail = $user->getEmail();
        }
        $user = $userRepository->find($userId);
        $em->remove($user);
      
        $em->flush();
        $this->sendEmailNoValidation();
        $this->addFlash('success', 'L\'utilisateur a bien été refusé');
        return $this->redirectToRoute('admin_validation_inscription_company_index');
    }

    /**
     * @Route("/company/{id}/show", name="company_show")
     */
    public function show ($id, CompanyRepository $companyRepository) 
    {
        $company = $companyRepository->find($id);

        return $this->render('admin/inscriptionValidation/company/show.html.twig', compact('company'));
    }

    public function sendEmailValidation() 
    {
        $mj = new \Mailjet\Client('768d23d39e95349c9486668619be34a1','691b14facbd2376c51c15da45301839b',true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "neymohetic@gmail.com",
              ],
              'To' => [
                [
                  'Email' => 'benjaminadida05@gmail.com',
                ]
              ],
              'Subject' => "Votre inscription est en attente de validation",
              'HTMLPart' => "<p>Vous etes validés</p>",
              'CustomID' => "AppGettingStartedTest"
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

    public function sendEmailNoValidation() 
    {
        $mj = new \Mailjet\Client('768d23d39e95349c9486668619be34a1','691b14facbd2376c51c15da45301839b',true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "neymohetic@gmail.com",
              ],
              'To' => [
                [
                  'Email' => 'benjaminadida05@gmail.com',
                ]
              ],
              'Subject' => "Votre inscription est en attente de validation",
              'HTMLPart' => "<p>Vous etes pas validés</p>",
              'CustomID' => "AppGettingStartedTest"
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

    /**
     * @Route("/particular", name="particular_index")
     */
    public function particularList(ParticularRepository $particularRepository)
    {
        $governanceId = $this->getUser()->getGovernanceUserInformation()->getGovernance()->getId();

        $particulars = $particularRepository->findParticularNotValidatedByGovernance($governanceId);

        return $this->render('admin/inscriptionValidation/particular/index.html.twig', compact('particulars'));
    }

    /**
     * @Route("/particular/{id}/show", name="particular_show")
     */
    public function showParticular ($id, ParticularRepository $particularRepository) 
    {
        $particular = $particularRepository->find($id);

        return $this->render('admin/inscriptionValidation/particular/show.html.twig', compact('particular'));
    }

    /**
     * @Route("/particular/{id}/validate", name="particular_validate")
     */
    public function particularValidate ($id, ParticularRepository $particularRepository, MailerInterface $mailer) 
    {
        $em = $this->getDoctrine()->getManager();
        $particular = $particularRepository->find($id);

        $particular->setValidated(true);
    
        $account = new Account();
        $account->setAccountNumber(rand(1, 100000));
        $account->setAvailableCash(0);
        $particular->setAccount($account);
        $em->persist($account);
        $em->persist($particular);
        $em->flush();

        $userEmail = $particular->getUser()->getEmail();

        $this->sendEmailValidation($mailer, $userEmail);

        $this->addFlash('success', 'L\'utilisateur a bien été validé');
        return $this->redirectToRoute('admin_validation_inscription_particular_index');
    }

    /**
     * @Route("/particular/{id}/no-validate", name="particular_no_validate")
     *
     * @param [type] $id
     * @return void
     */
    public function particularNoValidate($id, ParticularRepository $particularRepository, UserRepository $userRepository, MailerInterface $mailer) {
        $em = $this->getDoctrine()->getManager();
        $particular = $particularRepository->find($id);
        $userId = $particular->getUser()->getId();
        $userEmail = $particular->getUser()->getEmail();

        $em->remove($particular);
        
        $this->sendEmailNoValidation($mailer, $userEmail);
        $this->addFlash('success', 'L\'utilisateur a bien été refusé');
        return $this->redirectToRoute('admin_validation_inscription_particular_index');
    }

}
