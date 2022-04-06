<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Answer;
use App\Entity\Image;
use App\Form\AnswerForm;
use App\Service\ImageUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/task/{id}",methods="GET|POST", name="task_show")
     */
    public function show($id, SessionInterface $session, Request $request,  ImageUploader $imageUploader, TranslatorInterface $translator)
    {
        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($id);

        if (!$task) {
            throw $this->createNotFoundException(
                'Task not found '.$id
            );
        }

        $teamid = $session->get("team_id");
        $team = $this->getDoctrine()
            ->getRepository(Team::class)
            ->find($teamid);

        if (!$team) {
            throw $this->createNotFoundException(
                'Team not found '.$teamid
            );
        }

        $raceid = $session->get("race_id");
        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        if($task->getRace() != $race){
            return $this->render('peak/not_in_race.html.twig', ['race' => $race]);
        }

        if ($race->getStartShowingPeaks() > new \DateTime('NOW')){
            return $this->render('race/not_started.html.twig', ['race' => $race]);
        }

        if ($race->getStartLoggingPeaks() > new \DateTime('NOW')){
            return $this->render('peak/not_allowed.html.twig', ['race' => $race]);
        }

        // TODO: see https://symfony.com/doc/current/doctrine/associations.html#fetching-related-objects for performance optimization
        $answer = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByTaskAndTeam($id, $teamid);

        if ($answer)
        {
            $form_label = 'Upravit splnění úkolu';
            $is_answered = true;
        }
        else
        {
            $answer =new Answer();
            $answer->setTask($task);
            $answer->setTeam($team);
            $answer->setRace($race);
            $form_label = 'Potvrdit splnění úkolu';
            $is_answered = false;
        }

        $answer_form = $this->createForm(AnswerForm::class, $answer, ['is_answered' => $is_answered, 'form_label' => $form_label]);;

        $answer_form->handleRequest($request);

        if ($answer_form->isSubmitted())
        {
            $answer = $answer_form->getData();
            $manager = $this->getDoctrine()->getManager();

            if (new \DateTime('NOW') < $race->getStartLoggingPeaks() || new \DateTime('NOW') > $race->getStopLoggingPeaks())
            {
                // Peaks looging is not enabled
                $this->addFlash('danger', $translator->trans('Task_logging_disallowed'));
            }
            elseif ($answer_form->get('save')->isClicked())
            {
                // FIXME: delete old image file when new one is uploaded
                // FIXME: validate that uploaded file is image
                // TODO: resize image to reasonable size
                // TODO: convert to service https://symfony.com/doc/current/controller/upload_file.html#creating-an-uploader-service

                /** @var UploadedFile $imageFile */
                $imageFile = $answer_form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $newFilename = $imageUploader->upload($imageFile);
                    $image = new Image();
                    $image->setFilename($newFilename);
                    $manager->persist($image);
                    $answer->addImage($image);
                }
                
                $manager->persist($answer);
                $this->addFlash('primary', $translator->trans('Answer_logged'));              
            }

            elseif ($answer_form->get('delete')->isClicked())
            {
                if($answer->getImages()){
                    foreach( $answer->getImages() as $im){
                    $answer->removeImage($im);
                    }
                }
                $manager->remove($answer);         
                $this->addFlash('primary', $translator->trans('Answer_deleted'));  
            }

            $manager->flush();

            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }

        return $this->render('task/show.html.twig', ['task' => $task,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     'images' => $answer->getImages(),
                                                     'answer_form' => $answer_form->createView()]);
    }


    /**
     * @Route("/admin/racetasks/{raceid}", name="admin_task_table")
     */
    public function task_table($raceid)
    {
        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findByRace($raceid);

        $race = $this->getDoctrine()
            ->getRepository(Race::class)
            ->find($raceid);

        if (!$race) {
            throw $this->createNotFoundException(
                'Race not found '.$raceid
            );
        }

        return $this->render('admin/tasks_table.html.twig', ['race' => $race,'tasks' => $tasks]);
    }
}
