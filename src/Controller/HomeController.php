<?php
/**
 * Created by PhpStorm.
 * User: haj
 * Date: 10.01.2018
 * Time: 20:53
 */

namespace App\Controller;


use App\Entity\News;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\NewsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function homepage(AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }
        return $this->render('base.html.twig', Array('username' => $username));
    }

    /**
     * @Route("/news")
     */
    public function newspage(AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $news = $this->getDoctrine()->getManager()->getRepository(News::class)->findAll();

        return $this->render('news.html.twig', Array(
            'username' => $username,
            'news' => $news,
        ));
    }

    /**
     * @Route("/news/add")
     */
    public function addnew(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $date = new \DateTime('now');
            $date->format('Y-m-d');
            $titleRaw = str_replace(
                Array(' ','ą','ć','ę','ł','ń','ó','ś','ź','ż'),
                '-', $news->getTitle());
            $news ->setTitleRaw($titleRaw);
            $news ->setAuthor($this->getUser()->getLogin());
            $news ->setData($date);

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            return $this->redirect('/news');
        }

        return $this->render('newsadd.html.twig', Array(
            'username' => $username,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/news/delete/{title}")
     */
    public function news($title, AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $doctrine = $this->getDoctrine()->getManager();
        $repository = $doctrine->getRepository(News::class);
        $one = $repository->findOneBy(Array('title_raw' => $title));
        $doctrine->remove($one);
        $doctrine->flush();

        return $this->redirect('/news');
    }

    /**
     * @Route("/user")
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
     * @Route("/user/change-password")
     */
    public function changepassword(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $user = new User();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

        }

        return $this->render('changepassword.html.twig', Array(
            'username' => $username,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin")
     */
    public function adminpage(AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }
        return $this->render('admin.html.twig', Array('username' => $username));
    }
}