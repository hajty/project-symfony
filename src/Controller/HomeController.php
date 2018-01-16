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

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function homepage()
    {
        return $this->render('base.html.twig', Array());
    }

    /**
     * @Route("/user")
     */
    public function userpage()
    {
        return $this->render('user.html.twig', Array());
    }

    /**
     * @Route("/admin")
     */
    public function adminpage()
    {
        return $this->render('admin.html.twig', Array());
    }
}