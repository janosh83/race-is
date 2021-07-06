<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;
use App\Entity\Task;
use App\Entity\Peak;
use App\Entity\Team;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RaceController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/{_locale}/all_races", name="all_races", requirements={"_locale":"cz|en"})
     */
    public function index()
    {
        $user = $this->security->getUser();
        if($user)
        {
            $raceswhereMember = $this->getDoctrine()
                ->getRepository(Race::class)
                ->findAllWhereMember($user->getId());

            return $this->render('race/index.html.twig', [/*'races_leader' => $raceshereLeader,*/
                                                          'races_member' => $raceswhereMember]);
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }
    }

     /**
     * @Route("/{_locale}/race/{id}", name="race_show", requirements={"_locale":"cz|en"})
     */
    public function show($id, SessionInterface $session, TranslatorInterface $translator)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($id);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$id)
            );
        }

        if ($race->getStartShowingPeaks() > new \DateTime('NOW')){
            return $this->render('race/not_started.html.twig', ['race' => $race]);
        }

        $user = $this->security->getUser();

        /*$teamWhereLeader = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findLeaderByUserAndRace($user->getId(), $race);*/

        $teamWhereMember = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findMemberByUserAndRace($user->getId(), $race);

        $teamid = -1;

        // setore user info related to selected race into session
        $session->set("race_id",$id);
        $session->set("race_title",$race->getTitle());
        /*if ($teamWhereLeader != NULL){
            $teamid = $teamWhereLeader['id'];
            $session->set("is_leader", true);
            $session->set("is_member", false);
            $session->set("team_id",$teamWhereLeader['id']);
            $teamid = $teamWhereLeader['id'];
        }*/
        if ($teamWhereMember != NULL){
            $teamid = $teamWhereMember['id'];
            //$session->set("is_leader", false);
            $session->set("is_member", true);
            $session->set("team_id",$teamWhereMember['id']);
            $session->set("team_title", $teamWhereMember["title"]);
            $teamid = $teamWhereMember['id'];
        }

        if($teamid == -1){
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        $visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findVisitedByTeamAndRace($teamid, $race);

        $not_visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findNotVisitedByTeam($teamid, $race);

        $answered_tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findAnsweredByTeamAndRace($teamid, $race);

        $not_answered_tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findNotAnsweredByTeam($teamid, $race);

        return $this->render('race/show.html.twig', ['race' => $race, 
                                                     /*'teamWhereLeader'=>$teamWhereLeader, */
                                                     'teamWhereMember'=>$teamWhereMember, 
                                                     'visitedPeaks' => $visited_peaks,
                                                     'notVisitedPeaks' => $not_visited_peaks,
                                                     'answeredTasks' => $answered_tasks,
                                                     'notAnsweredTasks' => $not_answered_tasks]);
    }

    /**
     * @Route("/{_locale}/admin/raceresults/{raceid}", name="admin_race_results", requirements={"_locale":"cz|en"})
     */
    public function admin_race_results($raceid, TranslatorInterface $translator)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        $peak_results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks($raceid);
        $task_results = $this->getDoctrine()->getRepository(Team::class)->countByAnsweredTasks($raceid);

        return $this->render('admin/results.html.twig', ['race' => $race,
                                                         'peak_results' => $peak_results,
                                                         'task_results' => $task_results]);
    }

    /**
     * @Route("/{_locale}/race/results/{raceid}", name="public_race_results", requirements={"_locale":"cz|en"})
     */
    public function public_race_results($raceid, TranslatorInterface $translator)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        $peak_results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks($raceid);
        $task_results = $this->getDoctrine()->getRepository(Team::class)->countByAnsweredTasks($raceid);

        return $this->render('race/public_results.html.twig', ['race' => $race,
                                                               'peak_results' => $peak_results,
                                                               'task_results' => $task_results]);
    }

}