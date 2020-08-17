<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function show($id, SessionInterface $session, Request $request,  SluggerInterface $slugger)
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

        // TODO: see https://symfony.com/doc/current/doctrine/associations.html#fetching-related-objects for performance optimization
        $answer = $this->getDoctrine()
            ->getRepository(Answer::class)
            ->findByTaskAndTeam($id, $teamid);

        if ($answer)
        {
            $form_label = 'Upravit odpověď na úkol';
            $is_answered = true;
        }
        else
        {
            $answer =new Answer();
            $answer->setTask($task);
            $answer->setTeam($team);
            $answer->setRace($race);
            $form_label = 'Potvrdit odpověď na úkol';
            $is_answered = false;
        }

        $builder = $this->createFormBuilder($answer)
            ->add('note', CKEditorType::class, ['required' => false, 'sanitize_html' => true, 'config' => ['toolbar' => 'standard']])
            //->add('image', FileType::class, ['label' => 'Obrázek' ,'mapped' => false, 'required' => false])
            ->add('save', SubmitType::class, ['label' => $form_label]);
        
        if ($is_answered)
        {
            $builder->add('delete', SubmitType::class, [
                'label' => 'Zrušit odpověď na úkol', 
                'attr' => [ 'class' => 'btn-danger']]);
        }
        
        $answer_form = $builder->getForm();

        $answer_form->handleRequest($request);

        if ($answer_form->isSubmitted())
        {
            $answer = $answer_form->getData();
            $manager = $this->getDoctrine()->getManager();

            if ($answer_form->get('save')->isClicked())
            {
                // FIXME: delete old image file when new one is uploaded
                // FIXME: validate that uploaded file is image
                // TODO: resize image to reasonable size
                // TODO: convert to service https://symfony.com/doc/current/controller/upload_file.html#creating-an-uploader-service

                /** @var UploadedFile $imageFile */
/*                $imageFile = $visit_form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    $visit->setImageFilename($newFilename);
                }*/
                
                $manager->persist($answer);
                $this->addFlash('notice', 'Odpověď uložena');                
            }

            elseif ($answer_form->get('delete')->isClicked())
            {
                $manager->remove($answer);         
                $this->addFlash('notice', 'Odpověď smazána');
            }

            $manager->flush();

            return $this->redirectToRoute('race_show',array('id' => $raceid));
        }

        return $this->render('task/show.html.twig', ['task' => $task,
                                                     'race' => $race,
                                                     'team' => $team,
                                                     //'image' => $answer->getImageFilename(),
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
