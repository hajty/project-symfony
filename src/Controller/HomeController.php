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
    public function show()
    {
        $homeClass = "selected";
        return $this->render('base.html.twig', Array('homeClass' => $homeClass));
    }
}