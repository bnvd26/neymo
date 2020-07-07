<?php

namespace App\Controller\API;

use App\Entity\Post;
use App\Repository\CompanyRepository;
use App\Repository\LikeRepository;
use DateTime;
use App\Controller\API\ApiController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends ApiController
{
    private $companyRepository;

    private $likeRepository;

    private $em;

    public function __construct(CompanyRepository $companyRepository, LikeRepository $likeRepository, EntityManagerInterface $em)
    {
        $this->companyRepository = $companyRepository;
        $this->likeRepository = $likeRepository;
        $this->em = $em;
    }

    /**
     * List all posts of governance
     * 
     * @Route("/api/posts", name="api_posts_index", methods="GET")
     */
    public function index(): response
    {
        if ($this->getUser()->isParticular()) {

            $companies = $this->companyRepository->findCompanyValidatedByGovernance($this->getUser()->getParticular()->getGovernance()->getId());

            $posts = $this->postsOfAllCompaniesOfGovernance($companies);
        } elseif ($this->getUser()->isCompany()) {

            $companies = $this->companyRepository->findCompanyValidatedByGovernance($this->getUser()->getGovernanceOfCompany()->getId());

            $posts = $this->postsOfAllCompaniesOfGovernance($companies);
        }

        return $this->responseOk($posts);
    }

    /**
     * Create posts
     * 
     * @Route("/api/posts/create", name="api_posts_create", methods="POST")
     */
    public function create(Request $request): response
    {
        if ($this->getUser()->isCompany()) {
            $post = $this->deserialize($request->getContent(), Post::class);
            
            $post->setCompany($this->companyRepository->find($this->getUser()->getCompanyId()));

            $post->setDate(new DateTime());

            $this->em->persist($post);

            $this->em->flush();

            return $this->responseCreated([
                'Success' => "Le post a bien été crée",
            ]);
        };

        return $this->responseNotAllowed([
            'Error' => "Vous ne pouvez pas poster",
        ]);
    }

    /**
     * @param [type] $companies
     * @return array
     */
    public function postsOfAllCompaniesOfGovernance($companies): array
    {
        $posts = [];
        foreach ($companies as $company) {
            foreach ($company->getPosts() as $post) {
                $posts[] = $this->posts($post, $company);
            }
        }

        return $posts;
    }

    /**
     * Array returned to company/particular.
     *
     * @param [Entity] $post
     * @param [Entity] $company
     * @return array
     */
    public function posts($post, $company): array
    {
        return [
            'post_id' => $post->getId(),
            'title' => $post->getCompany()->getName(),
            'content' => $post->getContent(),
            'liked' => empty($this->likeRepository->findBy(['account' => $company->getAccount()->getId(), 'post' => $post])) ? false : true,
            'likes' => count($post->getLikes()),
            'date' => $post->getDate()
        ];
    }
}
