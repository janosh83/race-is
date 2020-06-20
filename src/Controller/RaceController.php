<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;
use App\Entity\Peak;
use App\Entity\Team;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
        $raceswhereMember = $this->getDoctrine()
            ->getRepository(Race::class)
            ->findAllWhereMember($user->getId());

        return $this->render('race/index.html.twig', [/*'races_leader' => $raceshereLeader,*/
                                                      'races_member' => $raceswhereMember]);
    }

     /**
     * @Route("/race/{id}", name="race_show")
     */
    public function show($id, SessionInterface $session)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($id);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$id
            );
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
        /*if ($teamWhereLeader != NULL){
            $teamid = $teamWhereLeader['id'];
            $session->set("is_leader", true);
            $session->set("is_member", false);
            $session->set("team_id",$teamWhereLeader['id']);
            $teamid = $teamWhereLeader['id'];
        }*/
        if ($teamWhereMember != NULL){
            $teamid = $teamWhereMember['id'];
            $session->set("is_leader", false);
            $session->set("is_member", true);
            $session->set("team_id",$teamWhereMember['id']);
            $session->set("team_title", $teamWhereMember["title"]);
            $teamid = $teamWhereMember['id'];
        }

        if($teamid == -1){
            throw $this->createNotFoundException(
                'Race '.$id.' not found, you are probably not signed.'
            );
        }

        $visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findVisitedByTeamAndRace($teamid, $race);

        $not_visited_peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findNotVisitedByTeam($teamid, $race);

        return $this->render('race/show.html.twig', ['race' => $race, 
                                                     /*'teamWhereLeader'=>$teamWhereLeader, */
                                                     'teamWhereMember'=>$teamWhereMember, 
                                                     'visitedPeaks' => $visited_peaks,
                                                     'notVisitedPeaks' => $not_visited_peaks]);
    }

}