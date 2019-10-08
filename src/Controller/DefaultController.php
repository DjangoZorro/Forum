<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findall();

        return $this->render('default/index.html.twig', [
            'categories' => $categories,
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/posts", name="all_posts")
     */
    public function showAction()
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findall();

        return $this->render('default/AllPosts.html.twig', [
            'posts' => $posts,
        ]);
    }
}
