<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Visit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $visit = $this->getDoctrine()
            ->getRepository(Visit::class)
            ->findByPeakAndTeam($id, $teamid);

        if ($visit)
        {
            $form_label = 'Zrušit návštěvu vrcholu';
            $not_visited = false;
        }
        else
        {
            $visit =new Visit();
            $visit->setPeak($peak);
            $visit->setTeam($team);
            $visit->setRace($race);
            $form_label = 'Potvrdit návštěvu vrcholu';
            $not_visited = true;
        }

        $form = $this->createFormBuilder($visit)
            //->add('note', TextareaType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => $form_label])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $visit = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            
            if ($not_visited)
            {
                $manager->persist($visit);
                $this->addFlash('notice', 'Vrchol zalogován');
            }
            else
            {
                $this->addFlash('notice', 'Vrchol odlogován');
                $manager->remove($visit);
            }
            
            $manager->flush();

            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }
        
        return $this->render('peak/show.html.twig', ['peak' => $peak,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     'form' => $form->createView()]);
    }

    /**
     * @Route("/peak_map/{raceid}", name="peak_map")
     */
    public function peak_map($raceid)
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

        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findMemberByUserAndRace($user->getId(), $race);

        // FIXME: code below is duplicate of RaceController class
        $teamWhereMember = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findMemberByUserAndRace($user->getId(), $race);

        $teamid = -1;
        if ($teamWhereMember != NULL){
            $teamid = $teamWhereMember['id'];
        }

        if($teamid == -1){
            throw $this->createAccessDeniedException('Not enough permission to see '.$raceid. 'probably not signed to the race.');
        }

        $visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findVisitedByTeamAndRace($teamid, $race);

        $not_visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findNotVisitedByTeam($teamid, $race);

        return $this->render('peak/map.html.twig', ['race_title' => $race->getTitle(),
                                                    'visited_peaks' => $visited_peaks, 
                                                    'nonvisited_peaks' => $not_visited_peaks]);

    }
}
