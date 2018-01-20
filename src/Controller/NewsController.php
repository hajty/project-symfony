<?php
/**
 * Created by PhpStorm.
 * User: haj
 * Date: 20.01.2018
 * Time: 12:51
 */

namespace App\Controller;


use App\Entity\News;
use App\Form\NewsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    /**
     * @Route("/news", name="newspage")
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
     * @Route("/news/add", name="news_add")
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

            return $this->redirectToRoute('newspage');
        }

        return $this->render('newsadd.html.twig', Array(
            'username' => $username,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/news/{title}", name="newpage")
     */
    public function newpage($title, AuthorizationCheckerInterface $authChecker)
    {
        $username = "";
        if ($authChecker->isGranted('ROLE_USER'))
        {
            $username = $this->getUser()->getLogin();
        }

        $doctrine = $this->getDoctrine()->getManager();
        $repository = $doctrine->getRepository(News::class);
        $new = $repository->findOneBy(Array('title_raw' => $title));

        return $this->render('new.html.twig', Array(
            'username' => $username,
            'new' => $new,
        ));
    }

    /**
     * @Route("/news/delete/{title}", name="news_delete")
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

        return $this->redirectToRoute('news');
    }
}