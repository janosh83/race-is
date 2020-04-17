<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Security;

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
    public function show(Request $request, $id)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($id);

        if (!$peak) {
            throw $this->createNotFoundException(
                'Peak not found '.$id
            );
        }

        
        $team = $this->security->getUser();

        // TODO: see https://symfony.com/doc/current/doctrine/associations.html#fetching-related-objects for performance optimization
        $not_visited = !$team->getVisitedPeaks()->contains($peak);

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

            $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findOneBy(['username' => $form_data['team_visited']]);

            if (!$team) {
                throw $this->createNotFoundException(
                    'Team not found '.$id
                );
            }

            $manager = $this->getDoctrine()->getManager();
            
            if ($not_visited)
            {
                $peak->addTeamsVisit($team);
            }
            else
            {
                $peak->removeTeamsVisit($team);
            }
            
            $manager->persist($peak);
            $manager->flush();

            // TODO some better redirect, which will show message Your visit has been successfully logged
            return $this->redirectToRoute('all_peaks');
        }
        
        return $this->render('peak/show.html.twig', ['peak' => $peak,
                                                     'not_visited' => $not_visited,
                                                     'form' => $form->createView()]);
    }

    /**
     * @Route("/allpeaks", name="all_peaks")
     */
    public function index()
    {
        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findAll();
               
        $visited = $this->security->getUser()->getVisitedPeaks();
        return $this->render('peak/index.html.twig', ['peaks' => $peaks, 'visited' => $visited]);
    }
}
