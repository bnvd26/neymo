<?php

namespace App\Controller\API;

use App\Entity\Favorite;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
use App\Repository\DirectoryRepository;
use App\Repository\FavoriteRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class FavoriteController extends AbstractController
{
    /**
     * @Route("/api/favorites", name="api_favorites", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Favorites of user listed"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="favorite")
     *
     * @param CompanyRepository $companyRepository
     *
     * @return Response
     */
    public function favorites(CompanyRepository $companyRepository)
    {
        $favorites = [];
        if ($this->getUser()->isParticular()) {
            foreach ($this->getUser()->getParticular()->getAccount()->getDirectory()->getFavorites() as $favorite) {
                if (is_null($favorite->getAccount()->getParticular())) {
                    $favorites[] = [
                     'account_id' => $favorite->getAccount()->getId(),
                    'name' => $favorite->getAccount()->getCompany()->getName(),
                    'type' => 'company'
                ];
                }

                if (!is_null($favorite->getAccount()->getParticular())) {
                    $favorites[] = [
                    'account_id' => $favorite->getAccount()->getId(),
                    'firstName' => $favorite->getAccount()->getParticular()->getFirstName(),
                    'lastName' => $favorite->getAccount()->getParticular()->getLastName(),
                    'type' => 'particular'
                 ];
                }
            }
        } elseif ($this->getUser()->isCompany()) {
            $company = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $company = $companyRepository->find($company->getId());
            }
            
            foreach ($company->getAccount()->getDirectory()->getFavorites() as $favorite) {
                if (is_null($favorite->getAccount()->getParticular())) {
                    $favorites[] = [
                    'account_id' => $favorite->getAccount()->getId(),
                    'name' => $favorite->getAccount()->getCompany()->getName(),
                    'type' => 'company'
                ];
                }

                if (!is_null($favorite->getAccount()->getParticular())) {
                    $favorites[] = [
                    'account_id' => $favorite->getAccount()->getId(),
                    'firstName' => $favorite->getAccount()->getParticular()->getFirstName(),
                    'lastName' => $favorite->getAccount()->getParticular()->getLastName(),
                    'type' => 'particular'
                 ];
                }
            }
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(json_encode($favorites));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/favorites/create", name="api_favorites_create", methods="POST")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Favorite added"
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
     *      @SWG\Property(property="accountId", type="int", example="170")
     *     )
     * )
     * @SWG\Tag(name="favorite")
     *
     * @param FavoriteRepository $favoriteRepository
     * @param CompanyRepository $companyRepository
     * @param DirectoryRepository $directoryRepository
     * @param AccountRepository $accountRepository
     * @param Request $request
     *
     * @return Response
     */
    public function create(FavoriteRepository $favoriteRepository, CompanyRepository $companyRepository, DirectoryRepository $directoryRepository, AccountRepository $accountRepository, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $dataDecoded = json_decode($request->getContent());
        if ($this->getUser()->isParticular()) {
            $directory = $directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
            
            if (!empty($favoriteRepository->findBy(['account' => $dataDecoded->accountId, 'directory' => $directory[0]->getId()]))) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent('Le favorite existe deja');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $favorite = new Favorite();
            $favorite->setDirectory($directory[0]);
            $favorite->setAccount($accountRepository->find($dataDecoded->accountId));
            
            $entityManager->persist($favorite);
            $entityManager->flush();
        } elseif ($this->getUser()->isCompany()) {
            $company = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $company = $companyRepository->find($company->getId());
            }
            $directory = $directoryRepository->findByAccount($company->getAccount()->getId());

            if (!empty($favoriteRepository->findBy(['account' => $dataDecoded->accountId, 'directory' => $directory[0]->getId()]))) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent('Le favoris existe deja');
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
            $favorite = new Favorite();
            $favorite->setDirectory($directory[0]);
            $favorite->setAccount($accountRepository->find($dataDecoded->accountId));
            
            $entityManager->persist($favorite);
            $entityManager->flush();
        }
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setContent('Le favoris à bien été ajouté');
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/api/favorites/{accountId}/delete", name="api_favorites_delete", methods="DELETE")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Favorite deleted"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Tag(name="favorite")
     *
     * @param $accountId
     * @param FavoriteRepository $favoriteRepository
     * @param CompanyRepository $companyRepository
     * @param DirectoryRepository $directoryRepository
     * @param AccountRepository $accountRepository
     * @param Request $request
     *
     * @return Response
     */
    public function delete($accountId, FavoriteRepository $favoriteRepository, CompanyRepository $companyRepository, DirectoryRepository $directoryRepository, AccountRepository $accountRepository, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($this->getUser()->isParticular()) {
            $directory = $directoryRepository->findByAccount($this->getUser()->getParticular()->getAccount()->getId());
            $account = $accountRepository->find($accountId);
            
            $favorite = $favoriteRepository->findBy(['account' => $account, 'directory' => $directory[0]->getId()]);

            $entityManager->remove($favorite[0]);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Le favori à bien été supprimé');
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
            
            $favorite = $favoriteRepository->findBy(['account' => $account, 'directory' => $directory[0]->getId()]);

            $entityManager->remove($favorite[0]);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent('Le favori à bien été supprimé');
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}
