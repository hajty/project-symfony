<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $error = $authUtils->getLastAuthenticationError();                  // get the login error if there is one
        $lastUsername = $authUtils->getLastUsername();                      // last username entered by the user

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
}
