<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;
use App\Entity\Team;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PeakController extends AbstractController
{
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

        $form = $this->createFormBuilder()
            ->add('peak_id', HiddenType::class)
            ->add('team_visited', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Log visit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $form_data = $form->getData();

            $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findOneBy(['username' => $form_data['team_visited']]);

            if (!$peak) {
                throw $this->createNotFoundException(
                    'Peak not found '.$id
                );
            }

            $manager = $this->getDoctrine()->getManager();
            
            $peak->addTeamsVisit($team);
            
            $manager->persist($peak);
            $manager->flush();

            // TODO some better redirect, which will show message Your visit has been successfully logged
            return $this->redirectToRoute('all_peaks');
        }
        
        return $this->render('peak/show.html.twig', ['peak' => $peak, 
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
               
        return $this->render('peak/index.html.twig', ['peaks' => $peaks]);
    }
}
