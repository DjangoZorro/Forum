<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\MakePostType;
use App\Entity\Reaction;
use App\Form\ReactionType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\ReactionRepository;
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
            
            $this->denyAccessUnlessGranted('ROLE_USER');
            $post = new Post();
            $post->setAuthor($user);
            $post->setTitle($form['title']->getData());
            $post->setText($form['text']->getData());
            $post->setCategory($form['category']->getData());

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

        return $this->render('default/AllPosts.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/viewpost/{id}", name="view_post", methods={"GET", "POST"})
     */
    public function viewAction(Post $post, Request $request): Response
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(Reaction::class);
        $reaction = $repository->findBy(
            ['post' => $post]
        );
        $form = $this->createForm(ReactionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->denyAccessUnlessGranted('ROLE_USER');
            $reaction = new Reaction();
            $reaction->setUser($user);
            $reaction->setPost($post);
            $reaction->setText($form['text']->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reaction);
            $entityManager->flush();

            return $this->redirectToRoute('view_post', ['id' => $post->getId()]);
        }

        return $this->render('default/ViewPost.html.twig', [
            'form' => $form->createView(),
            'reactions' => $reaction,
            'post' => $post,
        ]);
    }
}
