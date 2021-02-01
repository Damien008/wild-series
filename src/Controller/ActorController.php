<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/actors", name="actor_")
*/
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
             ->getRepository(Actor::class)
             ->findAll();

         return $this->render(
             'actor/index.html.twig',
             ['actors' => $actors]
         );
    }

    /**
     * Getting a actor by id
     *
     * @Route("/show/{id<^[0-9]+$>}", name="show")
     * @return Response
     */
    public function show(Actor $actor):Response
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor with id : '.$actor.' found in actor\'s table.'
            );
        }

        $programs = $actor->getPrograms();

        return $this->render('actor/show.html.twig', [
            'actor' => $actor, 'programs' => $programs
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actor);
            $entityManager->flush();

            $this->addFlash('success', 'The new actor has been created');

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/new.html.twig', [
            'actor' => $actor,
            'form' => $form->createView(),
        ]);
    }
}
