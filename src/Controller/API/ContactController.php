<?php

namespace App\Controller\API;

use App\Entity\Contacts;
use App\Entity\Directory;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
use App\Repository\ContactsRepository;
use App\Repository\DirectoryRepository;
use App\Repository\GovernanceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/api/contacts", name="api_contacts", methods="GET")
     */
    public function contacts(CompanyRepository $companyRepository)
    {
        $contacts = [];
        if ($this->getUser()->isParticular()) {
            foreach ($this->getUser()->getParticular()->getAccount()->getDirectory()->getContacts() as $contact) {
                if (is_null($contact->getAccount()->getParticular())) {
                    $contacts[] = [
                     'account_id' => $contact->getAccount()->getId(),
                    'name' => $contact->getAccount()->getCompany()->getName(),
                    'type' => 'company'
                ];
                }

                if (!is_null($contact->getAccount()->getParticular())) {
                    $contacts[] = [
                    'account_id' => $contact->getAccount()->getId(),
                    'firstName' => $contact->getAccount()->getParticular()->getFirstName(),
                    'lastName' => $contact->getAccount()->getParticular()->getLastName(),
                    'type' => 'particular'
                 ];
                }
            }
        } elseif ($this->getUser()->isCompany()) {
            $company = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $company = $companyRepository->find($company->getId());
            }
            
            foreach ($company->getAccount()->getDirectory()->getContacts() as $contact) {
                if (is_null($contact->getAccount()->getParticular())) {
                    $contacts[] = [
                    'account_id' => $contact->getAccount()->getId(),
                    'name' => $contact->getAccount()->getCompany()->getName(),
                    'type' => 'company'
                ];
                }

                if (!is_null($contact->getAccount()->getParticular())) {
                    $contacts[] = [
                    'account_id' => $contact->getAccount()->getId(),
                    'firstName' => $contact->getAccount()->getParticular()->getFirstName(),
                    'lastName' => $contact->getAccount()->getParticular()->getLastName(),
                    'type' => 'particular'
                 ];
                }
            }
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(json_encode($contacts));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/contacts/create", name="api_contacts_create", methods="POST")
     */
    public function create(ContactsRepository $contactsRepository, CompanyRepository $companyRepository, DirectoryRepository $directoryRepository, AccountRepository $accountRepository, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $dataDecoded = json_decode($request->getContent());
        
        if ($this->getUser()->isParticular()) {
            $directory = $directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
            
            if (!empty($contactsRepository->findBy(['account' => $dataDecoded->accountId, 'directory' => $directory[0]->getId()]))) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent('Le contact existe deja');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $contact = new Contacts();
            $contact->setDirectory($directory[0]);
            $contact->setAccount($accountRepository->find($dataDecoded->accountId));
            
            $entityManager->persist($contact);
            $entityManager->flush();
        } elseif ($this->getUser()->isCompany()) {
            $company = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $company = $companyRepository->find($company->getId());
            }
            $directory = $directoryRepository->findByAccount($company->getAccount()->getId());

            if (!empty($contactsRepository->findBy(['account' => $dataDecoded->accountId, 'directory' => $directory[0]->getId()]))) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent('Le contact existe deja');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $contact = new Contacts();
            $contact->setDirectory($directory[0]);
            $contact->setAccount($accountRepository->find($dataDecoded->accountId));
            
            $entityManager->persist($contact);
            $entityManager->flush();
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setContent('Le contact à bien été ajouté');
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/api/contacts/{accountId}/delete", name="api_contacts_delete", methods="DELETE")
     */
    public function delete($accountId, ContactsRepository $contactsRepository, CompanyRepository $companyRepository, DirectoryRepository $directoryRepository, AccountRepository $accountRepository, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->getUser()->isParticular()) {
            $directory = $directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
            $account = $accountRepository->find($accountId);
            
            $contact = $contactsRepository->findBy(['account' => $account, 'directory' => $directory[0]->getId()]);

            $entityManager->remove($contact[0]);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Le contact à bien été supprimé');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if ($this->getUser()->isCOmpany()) {
            $company = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $company = $companyRepository->find($company->getId());
            }
            $directory = $directoryRepository->findByAccount($company->getAccount()->getId());

            $account = $accountRepository->find($accountId);
            
            $contact = $contactsRepository->findBy(['account' => $account, 'directory' => $directory[0]->getId()]);

            $entityManager->remove($contact[0]);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Le contact à bien été supprimé');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
