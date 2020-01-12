<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;

use App\Entity\Comments;
use App\Entity\Category;
use App\Entity\News;
use App\Form\CategoryFormType;
use App\Form\NewsFormType;

class EditorController extends AbstractController
{
    private $em;


    public function __construct(EntityManagerInterface $entityManager)
    {
          $this->em = $entityManager;
    }

    /**
      * @Route("/editor", name="editor")
      */
    public function index()
    {
        return $this->render('editor/index.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

    /**
      * @Route("/news/{category}", name="editor_news")
      */
    public function news($category = 0)
    {
        return $this->render('editor/news.html.twig', [
            'news' => $this->em->getRepository('App\Entity\News')->getByCategory($category),
            'categories' => $this->em->getRepository('App\Entity\Category')-> findBy([], ['title' => 'ASC']),
        ]);
    }

    /**
      * @Route("/editor/article/add", name="add_article")
      */
    public function add_article(Request $request)
    {
        $form = $this->createForm(NewsFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            //dd($data->getCategories());
            $article = new News();
            $article->setTitle($data->getTitle());
            $article->setShort($data->getShort());
            $article->setContent($data->getContent());
            $article->setPublishedAt(new \DateTime('now'));
            foreach($data->getCategories() as $cat){
                $article->addCategory($cat);
            }
            $uploadedFile = $form['picture']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/assets/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($destination, $newFilename);
            $article->setPicture($newFilename);
            $this->em->persist($article);
            $this->em->flush();
            
            $this->addFlash('success', 'Article created!');
            return $this->redirectToRoute('editor_news');
        }

        return $this->render('editor/create_article.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    /**
      * @Route("/editor/article/edit/{article}", name="edit_article")
      */
    public function edit_article(News $article, Request $request){
        $form = $this->createForm(NewsFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['picture']->getData();
            
            $destination = $this->getParameter('kernel.project_dir').'/public/assets/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($destination, $newFilename);
            $article->setPicture($newFilename);
            
            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success', 'Article" '.$article->getTitle().'"  updated!');
            //return $this->redirectToRoute('editor_news', []);
        }

        return $this->render('editor/edit_article.html.twig', [
          'articleForm' => $form->createView()
        ]);
    }

    /**
      * @Route("/editor/article/delete/{article}", name="delete_article")
      */
    public function delete_article(News $article, Request $request){
        //dd($article);
        $this->em->remove($article);
        $this->em->flush();
        $this->addFlash('success', 'Article is deleted!');
        return $this->redirectToRoute('editor_news', []);
    }

    /**
      * @Route("/comments", name="editor_comments")
      */
    public function comments()
    {
        return $this->render('editor/comments.html.twig', [
            'comments' => $this->em->getRepository('App\Entity\Comments')->findBy([], ['publishedAt' => 'DESC']),
        ]);
    }
    
    /**
      * @Route("/comment/delete/{comment}", name="delete_comment")
      */
    public function delete_comment(Comments $comment){
        $comment->setIsDeleted(true);
        $this->em->persist($comment);
        $this->em->flush();
        $this->addFlash('success', 'Comment deleted!');
        return $this->redirectToRoute('editor_comments', []);
    }

    /**
      * @Route("/editor/category/add", name="add_category")
      */
    public function add_category(Request $request)
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $category = new Category();
            $category->setTitle($data->getTitle());
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Category added!');
            return $this->redirectToRoute('editor_news');
        }
        
        return $this->render('editor/add_category.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
      * @Route("/editor/category/edit/{category}", name="edit_category")
      */
    public function edit_category(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Category '.$category->getTitle().'  updated!');
            return $this->redirectToRoute('editor_news', []);
        }
        
        return $this->render('editor/edit_category.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    
}
