<?php
namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Race;
use App\Form\NewRaceFormType;
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

    /**
     * @Route("/admin/race/{raceid}", name="admin_race")
     */
    public function race_detail($raceid, Request $request)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findByRace($raceid);
        // NOTE: it shall be fine to pass empty object into template

        $delete_form = $this->createFormBuilder($race)
            ->add('delete', SubmitType::class, ['label'=>'Smazat závod'])
            ->getForm();

        $delete_form->handleRequest($request);
        if($delete_form->isSubmitted() && $delete_form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($race);
            $entityManager->flush();
            return $this->redirectToRoute('admin_home');
        }

        $peaks_form = $this->createFormBuilder()
            ->add('peaks_json', TextareaType::class, ['label'=>'Vrcholy'])
            ->add('submit_peaks', SubmitType::class, ['label'=>'Importovat vrcholy'])
            ->getForm();

        $peaks_form->handleRequest($request);
        if($peaks_form->isSubmitted() && $peaks_form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            
            $peaks_text = $peaks_form->get('peaks_json')->getData();
            $peaks_json = json_decode($peaks_text, true);

            foreach ($peaks_json as $p) {
                $peak = new Peak();
                $peak->setShortId($p['short_id']);
                $peak->setTitle($p["title"]);
                $peak->setDescription($p['description']);
                $peak->setLatitude($p['latitude']);
                $peak->setLongitude($p['longitude']);
                $peak->setRace($race);
                $entityManager->persist($peak);
            }

            $entityManager->flush();
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/race.html.twig', [
            'race' => $race, 
            'peaks' => $peaks,
            'delete_form' => $delete_form->createView(),
            'peaks_form' => $peaks_form->createView()]);
    }

    /**
     * @Route("/admin/newrace", name="admin_race_new")
     */
    public function race_new(Request $request)
    {
        $race = new Race();

        $form = $this->createForm(NewRaceFormType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $race->setTitle($form->get('title')->getData());
            $race->setDescription($form->get('description')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($race);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/newrace.html.twig', ['form' => $form->createView()]);
    }
}