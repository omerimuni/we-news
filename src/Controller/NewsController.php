<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\Comments;
use App\Form\CommentFormType;

class NewsController extends AbstractController
{
    private $em;


    public function __construct(EntityManagerInterface $entityManager)
    {
          $this->em = $entityManager;
    }

    /**
     * @Route("/", name="front")
     */
    public function index()
    {
        foreach($this->em->getRepository('App\Entity\Category')->findAll() as $category){
            $news[$category->getId()]['title'] = $category->getTitle();
            $news[$category->getId()]['news'] = $this->em->getRepository('App\Entity\News')->getByCategory($category->getId(), 3);
        }
        
        return $this->render('news/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/category/{category}", name="category")
     */
    function category($category = null, PaginatorInterface $paginator, Request $request)
    {   
        
        $queryBuilder = $this->em->getRepository('App\Entity\News')->getByCategoryQuery($category);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        if (!$category) {
            return $this->redirectToRoute('front');
        }else{

            return $this->render('news/category.html.twig', [
                'news' => $this->em->getRepository('App\Entity\News')->getByCategory($category),
                'category' => $this->em->getRepository('App\Entity\Category')->getCategory($category),
                'latest' => $this->em->getRepository('App\Entity\News')->getByCategory($category, 4),
                'categories' => $this->em->getRepository('App\Entity\Category')->findAll(),
                'pagination' => $pagination,
            ]);
        }
    }

    /**
     * @Route("/article/{article}", name="article")
     */
    public function article($article = NULL, Request $request)
    {
        $articleArray = $this->em->getRepository('App\Entity\News')->findOneBy(['id' => $article]);
        $views = $articleArray->getVisitors(); 
        $articleArray->setVisitors($views + 1);
        $this->em->persist($articleArray);
        $this->em->flush();

        $form = $this->createForm(CommentFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $comment = new Comments();
            $comment->setName($data->getName());
            $comment->setEmail($data->getEmail());
            $comment->setComment($data->getComment());
            $comment->setPublishedAt(new \DateTime());
            $comment->setNews($this->em->getRepository('App\Entity\News')->findOneBy(['id' => $article]));

            $this->em->persist($comment);
            $this->em->flush();
            $this->addFlash('success', 'Your comment posted!');
            return $this->redirectToRoute('article', ['article' => $article]);
        }else{
            $this->addFlash('error', 'Viga!');
        }
        
        $news = $this->em->getRepository('App\Entity\News')->findBy(['id' => $article]);
        if (!$news) {
            return $this->redirectToRoute('front');
        }else{
          return $this->render('news/article.html.twig', [
            'article' => $news[0],
            'comments' => $this->em->getRepository('App\Entity\Comments')->findBy(['news' => $article],  ['publishedAt' => 'DESC']),
            
            'latest' => $this->em->getRepository('App\Entity\News')->findBy(array(),array('id'=>'DESC'),4),
            'categories' => $this->em->getRepository('App\Entity\Category')->findAll(),
            'commentsForm' => $form->createView(),
          ]);
        }
    }
    
    

}
