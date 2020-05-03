<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Peak;

class PeakController extends AbstractController
{
    /**
     * @Route("/peak/{id}", name="peak_show")
     */
    public function show($id)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($id);

        if (!$peak) {
            throw $this->createNotFoundException(
                'Peak not found '.$id
            );
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('peak/show.html.twig', ['peak' => $peak]);
    }
}
