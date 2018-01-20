<?php
/**
 * Created by PhpStorm.
 * User: haj
 * Date: 10.01.2018
 * Time: 20:53
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
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
     * @Route("/admin", name="adminpage")
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