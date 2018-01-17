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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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

        return $this->render('news.html.twig', Array('username' => $username,
                                                           'news' => $news));
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