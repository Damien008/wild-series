<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    /**
    * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    public function navbartop(CategoryRepository $categoryRepository): Response
    {
        return $this->render('navbartop.html.twig', [
        'categories' => $categoryRepository->findBy([], ['id' => 'DESC'])
        ]);
    }
}