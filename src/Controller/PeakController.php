<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use App\Entity\Race;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PeakController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/peak/{id}", name="peak_show")
     */
    public function show($id, SessionInterface $session, Request $request)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($id);

        if (!$peak) {
            throw $this->createNotFoundException(
                'Peak not found '.$id
            );
        }

        $teamid = $session->get("team_id");
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);

        if (!$team) {
            throw $this->createNotFoundException(
                'Team not found '.$teamid
            );
        }

        $raceid = $session->get("race_id");
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        // TODO: see https://symfony.com/doc/current/doctrine/associations.html#fetching-related-objects for performance optimization
        $not_visited = !$team->getVisited()->contains($peak);

        if ($not_visited)
        {
            $form_label = 'Log visit';
        }
        else
        {
            $form_label = 'Unlog visit';
        }

        $form = $this->createFormBuilder()
            ->add('peak_id', HiddenType::class)
            ->add('team_visited', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => $form_label])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $form_data = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            
            if ($not_visited)
            {
                $peak->addVisited($team);
            }
            else
            {
                $peak->removeVisited($team);
            }
            
            $manager->persist($peak);
            $manager->flush();

            // TODO some better redirect, which will show message Your visit has been successfully logged
            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }
        
        return $this->render('peak/show.html.twig', ['peak' => $peak,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     'not_visited' => $not_visited,
                                                     'form' => $form->createView()]);
    }
}
