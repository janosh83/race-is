<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;
use App\Entity\Peak;
use Symfony\Component\Security\Core\Security;

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
        $team = $this->security->getUser();

        $races = $this->getDoctrine()
            ->getRepository(Race::class)
            ->findAll();
               
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

        $peaks = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->findByRace($race);

        return $this->render('race/show.html.twig', ['race' => $race, 'peaks' => $peaks]);
    } 


}