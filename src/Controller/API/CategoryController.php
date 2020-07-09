<?php

namespace App\Controller\API;

use App\Repository\GovernanceRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\API\ApiController;
use App\Repository\CategoryRepository;
use Swagger\Annotations as SWG;

class CategoryController extends ApiController
{
    /**
     * @Route("/api/categories", name="api_categories", methods="GET")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Categories listed"
     * )
     * @SWG\Tag(name="categories")
     *
     * @param CategoryRepository $categoryRepository
     *
     * @return Response
     */
    public function getAllCategories(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        $categoryArray = [];
    
        foreach ($categories as $category) {
            $categoryArray[] = [
                'id' => $category->getId(),
                'governance_name' => $category->getName()
            ];
        }

        return $this->responseOk($categoryArray);
    }
}
