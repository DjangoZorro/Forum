<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\MakePostType;
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
    public function index(Request $request)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findall();
        $form = $this->createForm(MakePostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated

            $post = new Post();
            $post->setAuthor($user);
            $post->setTitle($form['title']->getData());
            $post->setText($form['text']->getData());
            $post->setCategory($form['category']->getData());
            // var_dump($data['title']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/posts/{id}", name="posts", methods={"GET"})
     */
    public function showAction(Category $category)
    {
        $repository = $this->getDoctrine()->getRepository(Post::class)->findBy(['category' => $category]);

        return $this->render('default/AllPosts.html.twig', [
            'posts' => $repository,
        ]);
    }
}
