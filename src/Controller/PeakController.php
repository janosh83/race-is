<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Visit;
use App\Form\VisitForm;
use App\Service\ImageUploader;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/peak/{id}",methods="GET|POST", name="peak_show")
     */
    public function show($id, SessionInterface $session, Request $request,  ImageUploader $imageUploader)
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
            $form_label = 'Upravit návštěvu vrcholu';
            $is_visited = true;
        }
        else
        {
            $visit =new Visit();
            $visit->setPeak($peak);
            $visit->setTeam($team);
            $visit->setRace($race);
            $form_label = 'Potvrdit návštěvu vrcholu';
            $is_visited = false;
        }
        
        $visit_form = $this->createForm(VisitForm::class, $visit, ['is_visited' => $is_visited, 'form_label' => $form_label]);;

        $visit_form->handleRequest($request);

        if ($visit_form->isSubmitted())
        {
            $visit = $visit_form->getData();
            $manager = $this->getDoctrine()->getManager();

            if ($visit_form->get('save')->isClicked())
            {
                // FIXME: delete old image file when new one is uploaded
                // FIXME: validate that uploaded file is image
                // TODO: resize image to reasonable size
                // TODO: convert to service https://symfony.com/doc/current/controller/upload_file.html#creating-an-uploader-service

                /** @var UploadedFile $imageFile */
                $imageFile = $visit_form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $newFilename = $imageUploader->upload($imageFile);

                    $visit->setImageFilename($newFilename);
                }
                
                $manager->persist($visit);
                $this->addFlash('primary', 'Vrchol zalogován');                
            }

            elseif ($visit_form->get('delete')->isClicked())
            {
                $manager->remove($visit);         
                $this->addFlash('primary', 'Vrchol odlogován');
            }

            $manager->flush();

            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }

        return $this->render('peak/show.html.twig', ['peak' => $peak,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     'image' => $visit->getImageFilename(),
                                                     'visit_form' => $visit_form->createView()]);
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
}
