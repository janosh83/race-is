<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function change_password(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator)
    {

        $form = $this->createFormBuilder()
            ->add('old_password', PasswordType::class, ['label'=>$translator->trans('Old_password')])
            ->add('new_password', PasswordType::class, ['label'=>$translator->trans('New_password')])
            ->add('confirm_password', PasswordType::class, ['label'=>$translator->trans('New_password_again')])
            ->add('change_password', SubmitType::class, ['label'=>$translator->trans('Change_password')])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData(); 
            $new_pwd = $form->get('new_password')->getData(); 
            $new_pwd_confirm = $form->get('confirm_password')->getData();
            
            $user = $this->getUser();
            if($passwordEncoder->isPasswordValid($user, $old_pwd))
            {
                if($new_pwd == $new_pwd_confirm){
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('new_password')->getData()
                        )
                    );
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash('success',$translator->trans('Password_changed'));

                    return $this->redirectToRoute('app_home');
                }
            }
            else {
                $this->addFlash('danger',$translator->trans('Invalid_password'));
            }
        }

        return $this->render('security/change_password.html.twig', ['form' => $form->createView()]);
    }
}
