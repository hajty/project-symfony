<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeEmailType;
use App\Form\ChangePasswordType;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
//            $login = $form->get('login')->getData();
//            $email = $form->get('email')->getData();
            $login = $user->getLogin();
            $email = $user->getEmail();
            $doctrine = $this->getDoctrine()->getRepository(User::class);

            if (!$doctrine->findOneBy(Array(
                'login' => $login
            )))
            {
                if (!$doctrine->findOneBy(Array(
                    'email' => $email
                )))
                {
                    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($password);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash(
                        'info',
                        'Zarejestrowano pomyślnie!'
                    );
                }
                else
                {
                    $this->addFlash(
                        'info',
                        'Wybrany e-mail jest zajęty.'
                    );
                }
            }
            else
            {
                $this->addFlash(
                    'info',
                    'Wybrany login jest zajęty.'
                );
            }
        }

        return $this->render(
            'registration.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        $form->handleRequest($request);

        return $this->render(
            'login.html.twig',
            array(
                'form' => $form->createView(),
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/user", name="userpage")
     */
    public function userpage(AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }
        return $this->render('user.html.twig', Array('username' => $username));
    }

    /**
     * @Route("/user/change-password", name="user_change-password")
     */
    public function changepassword(Request $request, AuthorizationCheckerInterface $authChecker,
                                   UserPasswordEncoderInterface $passwordEncoder)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $user = new User();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $doctrine = $this->getDoctrine()->getManager();
            $repository = $doctrine->getRepository(User::class);
            $one = $repository->findOneBy(Array('login' => $this->getUser()->getLogin()));
            $one->setPassword($password);
            $doctrine->flush();

            $this->addFlash(
                'info',
                'Hasło zostało zmienione.'
            );
        }

        return $this->render('changepassword.html.twig', Array(
            'username' => $username,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/change-email", name="user_change-email")
     */
    public function changeemail(Request $request, AuthorizationCheckerInterface $authChecker,
                                   UserPasswordEncoderInterface $passwordEncoder)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $user = new User();
        $form = $this->createForm(ChangeEmailType::class, $user);

        $doctrine = $this->getDoctrine()->getManager();
        $repository = $doctrine->getRepository(User::class);
        $one = $repository->findOneBy(Array('login' => $this->getUser()->getLogin()));
        $old_email = $one->getEmail();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $email = $user->getEmail();

            $one->setEmail($email);
            $doctrine->flush();

            $this->addFlash(
                'info',
                'E-mail został zmieniony.'
            );

            return $this->redirectToRoute('user_change-email');
        }

        return $this->render('changeemail.html.twig', Array(
            'username' => $username,
            'form' => $form->createView(),
            'old_email' => $old_email,
        ));
    }
}
