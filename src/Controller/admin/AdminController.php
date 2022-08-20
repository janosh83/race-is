<?php
namespace App\Controller\admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Form\NewRaceFormType;
use App\Entity\Race;
use App\Entity\Team;
use App\Entity\Category;
use App\Entity\Registration;
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
     * @Route("/admin/visit/{raceid}/{teamid}", name="admin_visit_detail")
     */
    public function visit_detail($raceid, $teamid)
    {
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);
        
        if (!$team) {
            throw $this->createNotFoundException(
                'Team not found '.$teamid
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

        $visits = $this->getDoctrine()
            ->getRepository(Visit::class)
            ->findByRaceAndTeam($raceid, $teamid);

        $answers = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByRaceAndTeam($raceid, $teamid);

        return $this->render('admin/team_visits.html.twig', ['team' => $team,
                                                             'race' => $race,
                                                             /*'leader' => $team->getLeader(),*/
                                                             'members' => $team->getMember(),
                                                             'visits' => $visits,
                                                             'answers' => $answers]);
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

        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);
        
        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        $answers = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByRaceAndTeam($raceid, $teamid);

        return $this->render('admin/team_answers.html.twig', ['team' => $team,
                                                             'race' => $race,
                                                             /*'leader' => $team->getLeader(),*/
                                                             'members' => $team->getMember(),
                                                             'answers' => $answers]);
    }

    /**
     * @Route("/admin/peak/{peakid}/{raceid}", name="admin_peak_detail")
     */
    public function peak_detail($peakid,$raceid)
    {
        $peak = $this->getDoctrine()
            ->getRepository(Peak::class)
            ->find($peakid);

        if (!$peak) {
            throw $this->createNotFoundException(
                'Peak not found '.$peakid
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

        $visits = $this->getDoctrine()
            ->getRepository(Visit::class)
            ->findByPeakAndRace($peakid, $raceid);

        return $this->render('admin/peak.html.twig', ['peak' => $peak, 'visits' => $visits, 'race' => $race]);
    }

    /**
     * @Route("/admin/task/{taskid}/{raceid}", name="admin_task_detail")
     */
    public function task_detail($taskid, $raceid)
    {
        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($taskid);

        if (!$task) {
            throw $this->createNotFoundException(
                'Task not found '.$id
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

        $answers = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByTaskAndRace($taskid, $raceid);

        return $this->render('admin/task.html.twig', ['task' => $task, 'answers' => $answers, 'race' => $race]);
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

    private function add_tasks_from_json_text($tasks_text, $race)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tasks_json = json_decode($tasks_text, true);

        //dd($tasks_json);

        foreach ($tasks_json as $t) {
            $task = new Task();
            $task->setTitle($t["title"]);
            $task->setDescription($t['description']);
            $task->setPointsPerAnswer($t["points"]);
            $task->setRace($race);
            $entityManager->persist($task);
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

    private function add_users_from_json_text($text, $race, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
            
        $parsed_json = json_decode($text, true);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=sample.csv');

        $content = ["name, email, team, exits, password"];
        $i = 1;

        foreach($parsed_json as $team_json)
        {
            $team = $this->getDoctrine()->getRepository(Team::class)->findByTitle($team_json["team"]);
            if(!$team)
            {
                $team = new Team();
                $team->setTitle($team_json["team"]);
            }

            foreach ($team_json["members"] as $u) {
                $user = $this->getDoctrine()->getRepository(User::class)->findUserByEmail($u["email"]);

                $row = ['exist' => "true"];
                if(!$user)
                {
                    $user = new User();
                    $user->setName($u["name"]);
                    $user->setEmail($u["email"]);
                    $user->setRoles(['ROLE_USER']);
                    $row['exist'] = "false";
                }

                $password = $this->random_str(10);
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $entityManager->persist($user);
                $team->addMember($user);

                $row = ["name"=>$u["name"], "email"=>$u["email"], "team"=>$team_json["team"], "password"=>$password];
                $content[$i] = implode(",",$row);
                $i = $i + 1;
            }
 
            $category = $this->getDoctrine()->getRepository(Category::class)->findByTitle($team_json["category"]);
            if(!$category)
            {
                $category = new Category();
                $category->setTitle($team_json["category"]);
                $entityManager->persist($category);
            }
            $race->addCategory($category);
        
            $reg = new Registration();
            $reg->setTeam($team);
            $reg->setRace($race);
            $reg->setCategory($category);

            $entityManager->persist($race);
            $entityManager->persist($team);
            $entityManager->persist($reg);
        }
        $entityManager->flush();
        $response->setContent(implode("\n",$content));
        return $response;
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

        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findByRace($raceid);

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

        $add_tasks_form = $this->createFormBuilder()
            ->add('tasks_json', TextareaType::class, ['label'=>'Úkoly'])
            ->add('add_tasks', SubmitType::class, ['label'=>'Importovat úkoly'])
            ->getForm();

        // Import tasks
        $add_tasks_form->handleRequest($request);
        if($add_tasks_form->isSubmitted() && $add_tasks_form->isValid())
        {
            $this->add_tasks_from_json_text($add_tasks_form->get('tasks_json')->getData(), $race);

            return $this->redirectToRoute('admin_home');
        }

        $create_users_form = $this->createFormBuilder()
            ->add('users_json', TextareaType::class, ['label'=>'Uživatelé'])
            ->add('submit_users', SubmitType::class, ['label'=>'Vytvořit uživatele'])
            ->getForm();

        $create_users_form->handleRequest($request);
        if($create_users_form->isSubmitted() && $create_users_form->isValid())
        {
            $response = $this->add_users_from_json_text($create_users_form->get('users_json')->getData(), $race, $passwordEncoder);
            
            return $response;
        }

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findUserByRace($raceid);


        $stats = array(
            'num_of_visits'=> $this->getDoctrine()->getRepository(Visit::class)->countVisits($raceid),
            'num_of_checkpoints' => $this->getDoctrine()->getRepository(Peak::class)->countPeaks($raceid),
            'teams_without_visit' => $this->getDoctrine()->getRepository(Visit::class)->findTeamsWithoutVisit($raceid),
            'num_of_checkpoints_with_visit' => $this->getDoctrine()->getRepository(Peak::class)->countPeaksWithVisit($raceid));
        /* 
        - number of checkpoints with at least one visit
        - checkpoint with most visits
        */

        return $this->render('admin/race.html.twig', [
            'race' => $race, 
            'peaks' => $peaks,
            'users' => $users,
            'tasks' => $tasks,
            'delete_race_form' => $delete_race_form->createView(),
            'add_peaks_form' => $add_peaks_form->createView(),
            'add_tasks_form' => $add_tasks_form->createView(),
            'create_users_form' => $create_users_form->createView(),
            'stats' => $stats]);
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