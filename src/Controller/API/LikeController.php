<?php

namespace App\Controller\API;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LikeController extends AbstractController
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
     * @Route("/api/post/{id}/like", name="api_post_like", methods="GET")
     */
    public function like($id, Request $request, PostRepository $postRepository, AccountRepository $accountRepository, LikeRepository $likeRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $post = $postRepository->find($id);

        // dd($post->getLikes());
        // $likesCount = null;
        // foreach($post->getLikes() as $key => $like)
        // {
        //     if($like->getLiked() == true)
        //         $likesCount = $key + 1;
        // 
        // dd($likesCount);

        
        if ($this->getUser()->isParticular()) {
            $likedPost = $likeRepository->findBy([
                'account' => $this->getUser()->getParticular()->getAccount()->getId(), 
                'post' => $post
                ]);
            if (empty($likedPost)) {
                $like = new Like();
                $like->setLiked(true);
                $like->setAccount($accountRepository->find($this->getUser()->getParticular()->getAccount()->getId()));
                $like->setPost($post);
                $entityManager->persist($like);
                $entityManager->flush();
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent(json_encode([
                'Success' => "Le post a bien été like.",
                ]));
                $response->headers->set('Content-Type', 'application/json');
            
                return $response;
            }
            
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
            'Error' => "Vous avez déjà likez ce post",
            ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }

        if ($this->getUser()->isCompany()) {
            $comp = null;
            foreach ($this->getUser()->getCompanies() as $company) {
                $comp = $company;
            }
            
            $likedPost = $likeRepository->findBy(['account' => $company->getAccount()->getId(), 'post' => $post]);
            if (empty($likedPost)) {
                $like = new Like();
                $like->setLiked(true);
                $like->setAccount($accountRepository->find($company->getAccount()->getId()));
                $like->setPost($post);
                $entityManager->persist($like);
                $entityManager->flush();
                $response = new Response();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setContent(json_encode([
                'Success' => "Le post a bien été like.",
                ]));
                $response->headers->set('Content-Type', 'application/json');
            
                return $response;
            }
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
            'Error' => "Vous avez déjà likez ce post",
            ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }
    }

    /**
     * @Route("/api/post/{id}/dislike", name="api_post_dislike", methods="GET")
     */
    public function dislike($id, Request $request, PostRepository $postRepository, AccountRepository $accountRepository, LikeRepository $likeRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $post = $postRepository->find($id);
       
        if($this->getUser()->isCompany())
        {
            foreach ($this->getUser()->getCompanies() as $company) {
                $comp = $company;
            }
            
            $likedPost = $likeRepository->findBy(['account' => $company->getAccount()->getId(), 'post' => $post]);

            $entityManager->remove($likedPost)[0];
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
            'Success' => "Vous avez disliker ce post"
            ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }        
        
        if($this->getUser()->isParticular())
        {

            
            $likedPost = $likeRepository->findBy(['account' => $this->getUser()->getParticular()->getAccount()->getId(), 'post' => $post]);
            
            $entityManager->remove($likedPost[0]);
            $entityManager->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
            'Success' => "Vous avez disliker ce post",
            ]));
            $response->headers->set('Content-Type', 'application/json');
        
            return $response;
        }  
    }
}
