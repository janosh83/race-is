<?php
namespace App\Controller\admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Form\NewRaceFormType;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Visit;
use App\Entity\Peak;
use App\Entity\Answer;
use App\Entity\Task;


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

        $peak_results = $this->getDoctrine()->getRepository(Team::class)->countByVisistedPeaks($raceid);
        $task_results = $this->getDoctrine()->getRepository(Team::class)->countByAnsweredTasks($raceid);

        return $this->render('admin/results.html.twig', ['race' => $race,
                                                         'peak_results' => $peak_results,
                                                         'task_results' => $task_results]);
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
     * @Route("/admin/answer/{raceid}/{teamid}", name="admin_answer_detail")
     */
    public function answer_detail($raceid, $teamid)
    {
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);
        
        if (!$team) {
            throw $this->createNotFoundException(
                'Race not found '.$teamid
            );
        }

        $answers = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByRaceAndTeam($raceid, $teamid);

        return $this->render('admin/team_answers.html.twig', ['team' => $team,
                                                             'leader' => $team->getLeader(),
                                                             'members' => $team->getMember(),
                                                             'answers' => $answers]);
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
     * @Route("/admin/task/{taskid}", name="admin_task_detail")
     */
    public function task_detail($taskid)
    {
        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($taskid);

        if (!$task) {
            throw $this->createNotFoundException(
                'Task not found '.$id
            );
        }

        return $this->render('admin/task.html.twig', ['task' => $task]);
    }

    private function delete_all_peaks_by_race($race, $entityManager)
    {
        $query = $entityManager->createQuery('DELETE FROM App\Entity\Peak p WHERE p.race = :raceid');
        $query->setParameter('raceid', $race);
        $query->execute();
    }

    private function add_peaks_from_json_text($peaks_text, $race)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $peaks_json = json_decode($peaks_text, true);

        foreach ($peaks_json as $p) {
            $peak = new Peak();
            $peak->setShortId($p['short_id']);
            $peak->setTitle($p["title"]);
            $peak->setDescription($p['description']);
            $peak->setLatitude($p['latitude']);
            $peak->setLongitude($p['longitude']);
            $peak->setPointsPerVisit($p["points"]);
            $peak->setRace($race);
            $entityManager->persist($peak);
        }

        $entityManager->flush();
    }

    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     *
     * This function uses type hints now (PHP 7+ only), but it was originally
     * written for PHP 5 as well.
     * 
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     * 
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    private function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    private function add_teams_from_json_text($teams_text, $race, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
            
        $teams_json = json_decode($teams_text, true);

        foreach ($teams_json as $t) {
            $user = new User();
            $user->setName($t["name"]);
            $user->setEmail($t["email"]);
            $user->setRoles(['ROLE_USER']);
            $password = $this->random_str(10);
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
            // TODO: generate random password and send email to user

            $team = new Team();
            $team->setTitle($t["name"]);
            $team->setLeader($user);
            $team->addMember($user);
            $team->addSigned($race);

            $entityManager->persist($user);
            $entityManager->persist($team);

            $email = (new TemplatedEmail())
            ->from(new Address('mailer@picnicadventures.com', 'Picnic Adventures Mailbot'))
            ->to($t["email"])
            ->subject('['.$race->getTitle().']: Registrace nového uživatele')
            ->htmlTemplate('admin/new_user_email.html.twig')
            ->context([
                'name' => $t["name"],
                'race' => $race->getTitle(),
                'password' => $password,
            ])
        ;

        $mailer->send($email);
        }

        $entityManager->flush();
    }

    /**
     * @Route("/admin/race/{raceid}", name="admin_race")
     */
    public function race_detail($raceid, Request $request, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder)
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

        $delete_race_form = $this->createFormBuilder($race)
            ->add('delete', SubmitType::class, ['label'=>'Smazat závod'])
            ->getForm();

        // Delete race
        $delete_race_form->handleRequest($request);
        if($delete_race_form->isSubmitted() && $delete_race_form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            // TODO: delete all visits

            if($peaks){
                $this->delete_all_peaks_by_race($race, $entityManager);
            }

            // TODO: sign out all teams from race          

            $entityManager->remove($race);
            $entityManager->flush();
            return $this->redirectToRoute('admin_home');
        }

        $add_peaks_form = $this->createFormBuilder()
            ->add('peaks_json', TextareaType::class, ['label'=>'Vrcholy'])
            ->add('add_peaks', SubmitType::class, ['label'=>'Importovat vrcholy'])
            ->getForm();

        // Import peaks
        $add_peaks_form->handleRequest($request);
        if($add_peaks_form->isSubmitted() && $add_peaks_form->isValid())
        {
            $this->add_peaks_from_json_text($add_peaks_form->get('peaks_json')->getData(), $race);

            return $this->redirectToRoute('admin_home');
        }

        $delete_peaks_form = $this->createFormBuilder()
            ->add('delete_peaks', SubmitType::class, ['label'=>'Vymazat vrcholy'])
            ->getForm();

        // TODO peaks deletion

        $add_teams_form = $this->createFormBuilder()
            ->add('teams_json', TextareaType::class, ['label'=>'Týmy'])
            ->add('submit_teams', SubmitType::class, ['label'=>'Importovat týmy'])
            ->getForm();

        $add_teams_form->handleRequest($request);
        if($add_teams_form->isSubmitted() && $add_teams_form->isValid())
        {
            $this->add_teams_from_json_text($add_teams_form->get('teams_json')->getData(), $race, $mailer, $passwordEncoder);
            
            return $this->redirectToRoute('admin_home');
        }

        $delete_teams_form = $this->createFormBuilder()
            ->add('delete_teams', SubmitType::class, ['label'=>'Vymazat týmy'])
            ->getForm();

        $signin_teams_form = $this->createFormBuilder()
            ->add('signin_teams', SubmitType::class, ['label'=>'Přihlásit týmy'])
            ->getForm();
        
        $signout_teams_form = $this->createFormBuilder()
            ->add('signout_teams', SubmitType::class, ['label'=>'Odhlásit týmy'])
            ->getForm();

        return $this->render('admin/race.html.twig', [
            'race' => $race, 
            'peaks' => $peaks,
            'delete_race_form' => $delete_race_form->createView(),
            'add_peaks_form' => $add_peaks_form->createView(),
            'delete_peaks_form' => $delete_peaks_form->createView(),
            'add_teams_form' => $add_teams_form->createView(),
            'signin_teams_form' => $signin_teams_form->createView(),
            'signout_teams_form' => $signout_teams_form->createView()]);
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