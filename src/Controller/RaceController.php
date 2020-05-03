<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;

class RaceController extends AbstractController
{
    /**
     * @Route("/all_races", name="all_races")
     */
    public function index()
    {
        $races = $this->getDoctrine()
            ->getRepository(Race::class)
            ->findAll();
               
        //$visited = $this->security->getUser()->getVisitedPeaks();
        return $this->render('race/index.html.twig', ['races' => $races]);
    }

     /**
     * @Route("/race/{id}", name="race_show")
     */
    public function show($id)
    {
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($id);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$id
            );
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('race/show.html.twig', ['race' => $race]);
    } 


}