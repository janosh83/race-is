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
        
        $races = $this->getDoctrine()
            ->getRepository(Race::class)
            ->findAll();
               
        return $this->render('race/index.html.twig', ['races' => $races]);
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

        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findByRace($race);

        $user = $this->security->getUser();

        $teamWhereLeader = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findLeaderByUserAndRace($user->getId(), $race);

        $teamWhereMember = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findMemberByUserAndRace($user->getId(), $race);

        // setore user info related to selected race into session
        $session->set("race_id",$id);
        if ($teamWhereLeader != NULL){
            $session->set("is_leader", true);
            $session->set("is_member", false);
            $session->set("team_id",$teamWhereLeader['id']);
        }
        if ($teamWhereMember != NULL){
            $session->set("is_leader", false);
            $session->set("is_member", true);
            $session->set("team_id",$teamWhereMember['id']);
        }

        return $this->render('race/show.html.twig', ['race' => $race, 
                                                     'teamWhereLeader'=>$teamWhereLeader, 
                                                     'teamWhereMember'=>$teamWhereMember, 
                                                     'peaks' => $peaks]);
    } 


}