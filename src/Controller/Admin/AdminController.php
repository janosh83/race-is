<?php
namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Visit;
use App\Entity\Peak;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function admin_homepage()
    {
        $races = $this->getDoctrine()
            ->getRepository(Race::class)
            ->findAll();

        return $this->render('admin/admin.html.twig', ['races' => $races]);
    }

    /**
     * @Route("/admin/raceresults/{raceid}", name="admin_race_results")
     */
    public function race_results($raceid)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        $results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks($raceid);
        return $this->render('admin/results.html.twig', ['race' => $race,
                                                         'results' => $results]);
    }

    /**
     * @Route("/admin/racepeaks/{raceid}", name="admin_peak_table")
     */
    public function peak_table($raceid)
    {
        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findByRace($raceid);

        if (!$peaks) {
            throw $this->createNotFoundException(
                'No peaks found for race '.$raceid
            );
        }

        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        return $this->render('admin/peaks_table.html.twig', ['race' => $race,'peaks' => $peaks]);
    }

    /**
     * @Route("/admin/visit/{raceid}/{teamid}", name="admin_visit_detail")
     */
    public function visit_detail($raceid, $teamid)
    {
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);
        
        if (!$team) {
            throw $this->createNotFoundException(
                'Race not found '.$teamid
            );
        }

        $visits = $this->getDoctrine()
            ->getRepository(Visit::class)
            ->findByRaceAndTeam($raceid, $teamid);

        return $this->render('admin/team_visits.html.twig', ['team' => $team,
                                                             'leader' => $team->getLeader(),
                                                             'members' => $team->getMember(),
                                                             'visits' => $visits]);
    }

    /**
     * @Route("/admin/peak/{peakid}", name="admin_peak_detail")
     */
    public function peak_detail($peakid)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($peakid);

        if (!$peak) {
            throw $this->createNotFoundException(
                'Peak not found '.$id
            );
        }

        return $this->render('admin/peak.html.twig', ['peak' => $peak]);
    }
}