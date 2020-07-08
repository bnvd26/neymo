<?php

namespace App\Controller\API;

use App\Entity\Contacts;
use App\Repository\AccountRepository;
use App\Repository\ContactsRepository;
use App\Repository\DirectoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\API\ApiController;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;

class ContactController extends ApiController
{
    private $em;

    private $contactsRepository;

    private $accountRepository;

    private $directoryRepository;

    public function __construct(EntityManagerInterface $em, ContactsRepository $contactsRepository, AccountRepository $accountRepository, DirectoryRepository $directoryRepository)
    {
        $this->em = $em;
        $this->contactsRepository = $contactsRepository;
        $this->accountRepository = $accountRepository;
        $this->directoryRepository = $directoryRepository;
    }

    /**
     * @Route("/api/contacts", name="api_contacts", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Contacts of user listed"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="contact")
     */
    public function contacts()
    {
        $contacts = [];
         
        if ($this->getUser()->isParticular()) {
            
            if($this->emptyContact(($this->getUser()->getParticular()->getAccount()->getDirectory()->getContacts()[0]))) {
                return $this->responseOk(['Information' => 'Vous n\'avez pas de contact']);
            }          
                
            $contacts = $this->getContactListDetail($this->getUser()->getParticular()->getAccount()->getDirectory()->getContacts()); 
            
        } elseif ($this->getUser()->isCompany()) {
            $company = $this->getUser()->getCompany();
            
            if ($this->emptyContact(($company->getAccount()->getDirectory()->getContacts()[0]))) {
                return $this->responseOk(['Information' => 'Vous n\'avez pas de contact']);
            }
                
            $contacts = $this->getContactListDetail($company->getAccount()->getDirectory()->getContacts());
        }

        return $this->responseOk($contacts);
    }

    /**
     * Undocumented function
     *
     * @param [type] $userContact
     * @return array
     */
    private function getContactListDetail($userContact)
    {
        $contacts = [];

        foreach ($userContact as $contact) {
            if (!is_null($contact->getAccount()->getParticular())) {
                
                $contacts[] = [
                'account_id' => $contact->getAccount()->getId(),
                'firstName' => $contact->getAccount()->getParticular()->getFirstName(),
                'lastName' => $contact->getAccount()->getParticular()->getLastName(),
                'account_number' => $contact->getAccount()->getAccountNumber(),
                'type' => 'particular'
                ];
            } 

            if(is_null($contact->getAccount()->getParticular())) {

                    $contacts[] = [
                    'account_id' => $contact->getAccount()->getId(),
                    'name' => $contact->getAccount()->getCompany()->getName(),
                    'account_number' => $contact->getAccount()->getAccountNumber(),
                    'type' => 'company'
                    ];
                }
        }

        return $contacts;
    }

    private function emptyContact($contactList)
    {
        return is_null($contactList) ?? $this->responseOk(['Information' => 'Vous n\'avez pas de contact']);;
    }

    /**
     * @Route("/api/contacts/create", name="api_contacts_create", methods="POST")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Contact added in list of the user"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Object identifing the account.",
     *     required=true,
     *     @SWG\Schema(
     *      @SWG\Property(property="accountNumberId", type="int", example="14763")
     *     )
     * )
     * @SWG\Tag(name="contact")
     *
     * @param Request $request
     *
     * @return object|Response
     */
    public function create(Request $request)
    {
        $dataDecoded = json_decode($request->getContent());
        
        if ($this->getUser()->isParticular()) {
            $directory = $this->directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
        } elseif ($this->getUser()->isCompany()) {
            $directory = $this->directoryRepository->findByAccount($this->getUser()->getCompany()->getAccount()->getId());
        }
        
        if ($this->contactExist($dataDecoded, $directory)) {
            return $this->responseOk(['Information' => 'Le contact existe déja']);
        }

        $contact = new Contacts();

        $contact->setDirectory($directory[0]);

        $contact->setAccount($this->accountRepository->findBy(['account_number' => $dataDecoded->accountNumberId])[0]);

        $this->em->persist($contact);
        
        $this->em->flush();

        return $this->responseCreated(['Success' => 'Le contact a bien été ajouté']);
    }

    /**
     * Check if contact is in directory of current user
     *
     * @param [type] $dataDecoded
     * @param [type] $directory
     * @return bool
     */
    private function contactExist($dataDecoded, $directory)
    {
        return !empty($this->contactsRepository->findBy(['account' => $this->accountRepository->findBy(['account_number' => $dataDecoded->accountNumberId])[0]->getId(), 'directory' => $directory[0]->getId()]));
    }

    /**
     * @Route("/api/contacts/{accountId}/delete", name="api_contacts_delete", methods="DELETE")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Contact deleted from list of the user"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="contact")
     */
    public function delete($accountId)
    {
        if ($this->getUser()->isParticular()) {
            $directory = $this->directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
            
            $contact = $this->contactsRepository->findBy(['account' => $this->accountRepository->find($accountId), 'directory' => $directory[0]->getId()]);
        }
        if ($this->getUser()->isCompany()) {
            $directory = $this->directoryRepository->findByAccount($this->getUser()->getCompany()->getAccount()->getId());
            
            $contact = $this->contactsRepository->findBy(['account' => $this->accountRepository->find($accountId), 'directory' => $directory[0]->getId()]);
        }

        $this->em->remove($contact[0]);

        $this->em->flush();

        return $this->responseOk(['Information' => 'Le contact a bien été supprimé']);
    }
}
