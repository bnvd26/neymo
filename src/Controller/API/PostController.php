<?php

namespace App\Controller\API;

use App\Entity\Post;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
use App\Repository\LikeRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PostController extends AbstractController
{
    private function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    private function deserialize($data, $entity)
    {
        return $this->container->get('serializer')->deserialize($data, $entity, 'json');
    }


    /**
     * @Route("/api/posts/create", name="api_posts_create", methods="POST")
     */
    public function create(Request $request, CompanyRepository $companyRepository)
    {
        if (!is_null($this->getUser()->isCompany())) {
            foreach ($this->getUser()->getCompanies() as $company) {
                $companyId = $company->getId();
            }

            $entityManager = $this->getDoctrine()->getManager();
            $post = $this->deserialize($request->getContent(), Post::class);
            $post->setCompany($companyRepository->find($companyId));
            $post->setDate(new DateTime());
            $entityManager->persist($post);
            $entityManager->flush();

            $response = new Response();
            $response->setStatusCode(Response::HTTP_CREATED);
            $response->setContent(json_encode([
            'Success' => "Le post a bien été céeer",
        ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        };

        $response = new Response();
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        $response->setContent(json_encode([
            'Error' => "Vous ne pouvez pas poster",
        ]));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
     * @Route("/api/posts", name="api_posts_index", methods="GET")
     */
    public function index(CompanyRepository $companyRepository, LikeRepository $likeRepository)
    {
        $posts = [];

        if ($this->getUser()->isParticular()) {
            $companies = $companyRepository->findCompanyValidatedByGovernance($this->getUser()->getParticular()->getGovernance()->getId());

            foreach ($companies as $company) {
                foreach ($company->getPosts() as $post) {
                    
                    $posts[] = [
                        'title' => $post->getTitle(),
                        'content' => $post->getContent(),
                        'liked' => empty($likeRepository->findBy(['account' => $this->getUser()->getParticular()->getAccount()->getId(), 'post' => $post])) ? false : true,
                        'likes' => count($post->getLikes())
                    ];
                }
            }
        } elseif(!is_null($this->getUser()->isCompany())) {
            $companyGovernanceId = null;
            foreach($this->getUser()->getCompanies() as $company) {
                $companyGovernanceId = $company->getGovernance()->getId();
            }
            $companies = $companyRepository->findCompanyValidatedByGovernance($companyGovernanceId);
            foreach ($companies as $company) {
                foreach ($company->getPosts() as $post) {
                    
                    $posts[] = [
                        'title' => $post->getTitle(),
                        'content' => $post->getContent(),
                        'liked' => empty($likeRepository->findBy(['account' => $company->getAccount()->getId(), 'post' => $post])) ? true : false,
                        'likes' => count($post->getLikes())
                    ];
                }
            }
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent(json_encode($posts));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}
