<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Visit;
use App\Entity\Image;
use App\Form\VisitForm;
use App\Service\ImageUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route("/{_locale}/peak/{id}",methods="GET|POST", name="peak_show", requirements={"_locale":"cz|en"})
     */
    public function show($id, SessionInterface $session, Request $request,  ImageUploader $imageUploader, TranslatorInterface $translator)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($id);

        if (!$peak) {
            throw $this->createNotFoundException(
                $translator->trans('Peak_not_found'.$id)
            );
        }

        if(!$session->has("team_id")){
            return $this->redirectToRoute('app_home');
        }
        
        $teamid = $session->get("team_id");
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);

        if (!$team) {
            throw $this->createNotFoundException(
                $translator->trans('Team_not_found'.$teamid)
            );
        }

        $raceid = $session->get("race_id");
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        if($peak->getRace() != $race){
            return $this->render('peak/not_in_race.html.twig', ['race' => $race]);
        }

        if ($race->getStartShowingPeaks() > new \DateTime('NOW')){
            return $this->render('race/not_started.html.twig', ['race' => $race]);
        }

        if ($race->getStartLoggingPeaks() > new \DateTime('NOW')){
            return $this->render('peak/not_allowed.html.twig', ['race' => $race]);
        }

        // TODO: see https://symfony.com/doc/current/doctrine/associations.html#fetching-related-objects for performance optimization
        $visit = $this->getDoctrine()
            ->getRepository(Visit::class)
            ->findByPeakAndTeam($id, $teamid);

        if ($visit)
        {
            $form_label =  $translator->trans('Edit_visit');
            $is_visited = true;
        }
        else
        {
            $visit =new Visit();
            $visit->setPeak($peak);
            $visit->setTeam($team);
            $visit->setRace($race);
            $form_label = $translator->trans('Confirm_visit');
            $is_visited = false;
        }
        
        $visit_form = $this->createForm(VisitForm::class, $visit, ['is_visited' => $is_visited, 'form_label' => $form_label]);;

        $visit_form->handleRequest($request);

        if ($visit_form->isSubmitted())
        {
            $visit = $visit_form->getData();
            $manager = $this->getDoctrine()->getManager();

            if (new \DateTime('NOW') > $race->getStopLoggingPeaks())
            {
                // Peaks looging is not enabled
                $this->addFlash('danger', $translator->trans('Visit_logging_disallowed'));
            }

            elseif ($visit_form->get('save')->isClicked())
            {
                // FIXME: delete old image file when new one is uploaded
                // FIXME: validate that uploaded file is image
                // TODO: resize image to reasonable size

                /** @var UploadedFile $imageFile */
                $imageFile = $visit_form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $newFilename = $imageUploader->upload($imageFile);
                    $image = new Image();
                    $image->setFilename($newFilename);
                    $manager->persist($image);

                    $visit->addImage($image);
                }
                
                $manager->persist($visit);
                $this->addFlash('primary', $translator->trans('Visit_logged'));                
            }

            elseif ($visit_form->get('delete')->isClicked())
            {
                $manager->remove($visit);         
                $this->addFlash('primary', $translator->trans('Visit_unlogged'));
            }

            $manager->flush();

            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }

        return $this->render('peak/show.html.twig', ['peak' => $peak,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     'images' => $visit->getImages(),
                                                     'visit_form' => $visit_form->createView()]);
    }

    /**
     * @Route("/{_locale}/peak_map/{raceid}", name="peak_map", requirements={"_locale":"cz|en"})
     */
    public function peak_map($raceid, TranslatorInterface $translator)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        if ($race->getStartShowingPeaks() > new \DateTime('NOW')){
            return $this->render('race/not_started.html.twig', ['race' => $race]);
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
            throw $this->createAccessDeniedException($translator->trans('Not_enough_permissions').$raceid.$translator->trans('Not_loggend_into_race'));
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

    private function get_race_peaks_data($raceid, TranslatorInterface $translator)
    {
        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findByRace($raceid);

        if (!$peaks) {
            throw $this->createNotFoundException(
                $translator->trans('No_peaks_found_for_race'.$raceid)
            );
        }

        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                $translator->trans('Race_not_found'.$raceid)
            );
        }

        /* NOTE: it is safe hre to skip check if it is allowed to show peaks 
                 as this code is called only for admin tasks. */

        return ['race' => $race,'peaks' => $peaks];
    }

    /**
     * @Route("/{_locale}/admin/racepeaks/{raceid}", name="admin_peak_table", requirements={"_locale":"cz|en"})
     */
    public function peak_table($raceid)
    {
        return $this->render('admin/peaks_table.html.twig', $this->get_race_peaks_data($raceid));
    }

    /**
     * @Route("/{_locale}/admin/roadbook/{raceid}", name="admin_roadbook", requirements={"_locale":"cz|en"})
     */
    public function roadbook($raceid)
    {
        return $this->render('admin/roadbook.html.twig', $this->get_race_peaks_data($raceid));
    }
}
