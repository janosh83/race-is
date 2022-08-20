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
     * @Route("/all_races", name="all_races")
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
     * @Route("/race/{id}", name="race_show")
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
     * @Route("/admin/raceresults/{raceid}", name="admin_race_results")
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

        $teams = $this->getDoctrine()->getRepository(Team::class)->findByRace($raceid);
        $task_results = $this->getDoctrine()->getRepository(Team::class)->countByAnsweredTasks($raceid);
        $peaks_results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks($raceid);

        foreach($teams as $team)
        {
            $results[$team["title"]] = [
                "order_low"=>0, 
                "order_high"=>0,
                "title"=> $team["title"], 
                "teamid"=> $team["id"], 
                "race_category"=> $team["race_category"], 
                "peak_points" => 0,
                "task_points" => 0,
                "total_points" => 0];
        }

        foreach($task_results as $team_result)
        {
            $results[$team_result["title"]]["task_points"] = $team_result["task_points"];
        }

        foreach($peaks_results as $team_result)
        {
            $results[$team_result["title"]]["peak_points"] = $team_result["peak_points"];
        }

        foreach($teams as $team)
        {
            $results[$team["title"]]["total_points"] = $results[$team["title"]]["peak_points"] + $results[$team["title"]]["task_points"];
        }

        usort($results, function($r1, $r2){

            if($r1["total_points"] == $r2["total_points"])
            {
                return 0;
            }
            else
            {
                return $r1["total_points"] > $r2["total_points"] ? -1 : 1;
            }
        });

        

        $i = 0;
        $same_points_low = 0;
        $same_points_high = 0;
        $prev_points = 0;
        foreach($teams as $team)
        {
            if($prev_points != $results[$i]["total_points"])
            {
                $results[$i]["order_low"] = $i+1;
                $results[$i]["order_high"] = $i+1;
                $prev_points = $results[$i]["total_points"];
                $same_points_low = 0;
                $same_points_high = 0;
            }
            else
            {
                if($same_points_low == 0 && $same_points_high == 0)
                {
                    $k = $i;
                    while($k < count($teams) && $prev_points == $results[$k]["total_points"])
                    {
                        $k++;
                    }
                    
                    $same_points_low = $i;
                    $same_points_high = $k;
                    if($i > 0)
                    {
                        $results[$i-1]["order_low"] = $same_points_low;
                        $results[$i-1]["order_high"] = $same_points_high;
                    }
                }
                $results[$i]["order_low"] = $same_points_low;
                $results[$i]["order_high"] = $same_points_high;
            }

            $i++;
        }

        //dd($results);

        /*dd($teams, $task_results, $peaks_results, $results);

        $results = [
            [
            "order"=>1, 
            "title"=> "Tym 1", 
            "race_category"=> "Auta", 
            "peak_points" => 21,
            "task_points" => 10,
            "total_points" => 31],
            ["order"=>2, 
            "title"=> "Tym 2", 
            "race_category"=> "Auta", 
            "peak_points" => 15,
            "task_points" => 8,
            "total_points" => 23]];*/

        return $this->render('admin/results.html.twig', ['race' => $race,
                                                         'results' => $results]);
    }

    /**
     * @Route("/race/results/{raceid}", name="public_race_results")
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