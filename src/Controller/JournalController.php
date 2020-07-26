<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\JournalPost;

class JournalController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/journal/race/{raceid}",methods="GET|POST", name="journal_index")
     */
    public function index($raceid, Request $request, SessionInterface $session)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        $user = $this->security->getUser();

        $teamid = $session->get("team_id");
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);

        $posts = $this->getDoctrine()
            ->getRepository(JournalPost::class)
            ->findByRaceAndTeam($raceid, $teamid);

        $post = new JournalPost();
        $post->setRace($race);
        $post->setTeam($team);
        $post->setAuthor($user);

        $post_form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('text', TextareaType::class)
            ->add('date',DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Přidat záznam'])
            ->getForm();

        $post_form->handleRequest($request);

        if ($post_form->isSubmitted())
        {
            $post = $post_form->getData();
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('journal_index',array('raceid' => $raceid));

        }

        return $this->render('journal/index.html.twig', [
                'race' => $race, 
                'team' => $team, 
                'posts' => $posts,
                'new_post_form' => $post_form->createView()]);
    }

    /**
     * @Route("/journal/show/{id}", name="journal_show")
     */
    public function show($id)
    {
        $post = $this->getDoctrine()
            ->getRepository(JournalPost::class)
            ->find($id);
        
        return $this->render('journal/show.html.twig', [
            'post' => $post]);
    }
}
