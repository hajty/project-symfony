<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
                    $user->setAdminFlag(false);

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
    public function loginAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

        }

        return $this->render(
            'login.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
