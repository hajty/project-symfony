<?php
/**
 * Created by PhpStorm.
 * User: haj
 * Date: 10.01.2018
 * Time: 20:53
 */

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HomeController extends Controller
{

    private $username;

    /**
     * @Route("/")
     */
    public function homepage(AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $this->username = $this->getUser()->getLogin();
        }
        return $this->render('base.html.twig', Array('username' => $this->username));
    }

    /**
     * @Route("/user")
     */
    public function userpage()
    {
        return $this->render('user.html.twig', Array('username' => $this->username));
    }

    /**
     * @Route("/admin")
     */
    public function adminpage()
    {
        return $this->render('admin.html.twig', Array('username' => $this->username));
    }
}