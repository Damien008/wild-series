<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Program;

/**
* @Route("/categories", name="category_")
*/

class CategoryController extends AbstractController
{
    /**
     * Show all rows from Category’s entity
    * @Route("/", name="index")
    * @return Response A response instance
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
             ->getRepository(category::class)
             ->findAll();

         return $this->render(
             'category/index.html.twig',
             ['categories' => $categories]
         );
    }

    /**
     * @Route("/show/{categoryName}", name="show")
     * @return Response
     */
    public function show(string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

            if (!$category) {
                throw $this->createNotFoundException(
                    'No program with category name : '.$categoryName.' found in category\'s table.'
                );
            }

            $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );

            return $this->render('category/show.html.twig', [
                'category' => $category, 'programs' => $programs
            ]);

    }        
}