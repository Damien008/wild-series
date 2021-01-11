<?php
// src/Controller/ProgramController.php
namespace App\Controller;


use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Service\Slugify;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchPogramType;
use App\Repository\ProgramRepository;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
* @Route("/programs", name="program_")
*/
class ProgramController extends AbstractController
{

    /**
     * Show all rows from Program’s entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(Request $request, ProgramRepository $programRepository): Response
    {
         $form = $this->createForm(SearchPogramType::class);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * The controller for the program add form
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Persist Program Object
            // Set the program's owner
            $program->setOwner($this->getUser());
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();
            // Envoi de mail
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);
            // Finally redirect to programs list
            return $this->redirectToRoute('program_index');
        }
        // Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * Getting a program by id
     *
     * @Route("/{slug}", name="show")
     * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     */
    public function show(Program $program):Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$program.' found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program, 'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/{program}/seasons/{season}", name="season_show")
     */
    public function showSeason(Program $program, Season $season): Response
    {
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program, 'season' => $season, 'episodes' => $episodes
        ]);
    }

    /**
     * @Route("/{program}/seasons/{season}/episodes/{episode}", name="episode_show")
     */
    public function showEpisode(Request $request, Program $program, Season $season, Episode $episode): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     * 
     */
    public function edit(Request $request, Program $program): Response
    {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

}