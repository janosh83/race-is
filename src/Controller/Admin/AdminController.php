<?php
namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Race;
use App\Entity\Team;


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
     * @Route("/admin/race/{raceid}", name="admin_race_results")
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
}