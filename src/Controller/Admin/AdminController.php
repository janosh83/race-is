<?php
namespace App\Controller\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use App\Service\ResultCalculator;
use App\Entity\Team;
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function admin_homepage()
    {
        return $this->render('admin/admin.html.twig');
    }
    /**
     * @Route("/admin/results", name="admin_race_results")
     */
    /* public function race_results()
    {
        $results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks();
        return $this->render('admin/results.html.twig', ['results' => $results]);
    } */
}