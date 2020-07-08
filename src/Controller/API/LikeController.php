<?php

namespace App\Controller\API;

use App\Entity\Like;
use App\Repository\AccountRepository;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\API\ApiController;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;

class LikeController extends ApiController
{
    private $em;

    private $postRepository;

    private $accountRepository;

    private $likeRepository;

    public function __construct(EntityManagerInterface $em, PostRepository $postRepository, AccountRepository $accountRepository, LikeRepository $likeRepository)
    {
        $this->em = $em;
        $this->accountRepository = $accountRepository;
        $this->postRepository = $postRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * @Route("/api/post/{id}/like", name="api_post_like", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Post liked"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="like")
     *
     * @param $id
     *
     * @return Response
     */
    public function like($id)
    {
        $post = $this->postRepository->find($id);

        $likedPost = $this->getUser()->isParticular() ? $this->likeRepository->findBy(['account' => $this->getUser()->getParticular()->getAccount()->getId(), 'post' => $post]) : $this->likeRepository->findBy(['account' => $this->getUser()->getCompany()->getAccount()->getId(), 'post' => $post]);
        
        $userType = $this->getUser()->isParticular() ? $this->getUser()->getParticular() : $this->getUser()->getCompany();
        
        if (empty($likedPost)) {
            return $this->createLike($userType, $post);
        }
        
        return $this->responseNotAcceptable([
            'Error' => "Vous avez déjà likez ce post",
            ]);
    }

    /**
     * @Route("/api/post/{id}/dislike", name="api_post_dislike", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Post disliked"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     *     )
     * @SWG\Tag(name="like")
     *
     * @param $id
     * @param PostRepository $postRepository
     * @param LikeRepository $likeRepository
     *
     * @return Response
     */
    public function dislike($id, PostRepository $postRepository, LikeRepository $likeRepository)
    {
        $post = $postRepository->find($id);

        $userType = $this->getUser()->isParticular() ? $this->getUser()->getParticular() : $this->getUser()->getCompany();

        $likedPost = $likeRepository->findBy(['account' => $userType->getAccount()->getId(), 'post' => $post]);

        $this->em->remove($likedPost[0]);

        $this->em->flush();

        return $this->responseOk([
            'Success' => "Vous avez disliker ce post",
            ]);
    }

    public function createLike($userType, $post)
    {
        $like = new Like();
        $like->setLiked(true);
        $like->setAccount($this->accountRepository->find($userType->getAccount()->getId()));
        $like->setPost($post);
        $this->em->persist($like);
        $this->em->flush();
        return $this->responseCreated([
                'Success' => "Le post a bien été like.",
                ]);
    }
}
