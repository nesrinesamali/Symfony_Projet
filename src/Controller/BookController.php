<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\ResearchType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'addBook')]
    public function addBook(ManagerRegistry $manager, Request $request): Response
    {
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->add('Save',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $book->setPublished(true);
            $nb=$book->getAuthor()->getNbBooks()+1;
            $book->getAuthor()->setNbBooks($nb);
            $em=$manager->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('showBooks');
        }

        return $this->render('book/add.html.twig', ['form' => $form->createView()]);
    }



    #[Route('/showBooks', name:'showBooks')]
    public function showBooks(BookRepository $bookRepository){
        $published=$bookRepository->findBy(['published' => true]);
        $unpublished=$bookRepository->findBy(['published' => false]);

        return $this->render('book/showBooks.html.twig',['books'=>$published,'unpublishedBooks'=>$unpublished]);
    }

    #[Route('/editBooks/{id}', name:'editBooks')]
    public function editBooks($id,BookRepository $bookRepository, Request $request, ManagerRegistry $manager){
        $books=$bookRepository->find($id);
        $form=$this->createForm(BookType::class,$books);
        $form->add('Save',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em=$manager->getManager();
            $em->persist($books);
            $em->flush();

        }

        return $this->render('book/editBooks.html.twig',['form'=>$form->createView()]);
    }

    #[Route('/deleteBooks/{id}', name:'deleteBooks')]
    public function deleteBooks($id,BookRepository $bookRepository, ManagerRegistry $manager){
        $book=$bookRepository->find($id);
        $em=$manager->getManager();
        $em->remove($book);
        $em->flush();
        $list=$bookRepository->findAll();
        

        return $this->render('book/showBooks.html.twig',['books'=>$list]);
    }

    #[Route('/showDetails/{id}', name:'showDetails')]
    public function showDetails($id,BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        return $this->render('book/showDetails.html.twig', ['book'=>$book]);
    }

    #[Route('/showQueryBuilder', name:'showQueryBuilder')]
    public function showQueryBuilder(BookRepository $repo){
        $list=$repo->ShowAllBook();
        return $this->render('book/showBooks.html.twig',['books'=>$list]);

    }
    
    #[Route('/showBQL', name: 'showBQL')]
    public function showBQL(BookRepository $repo){
        $list=$repo->showALLDQL();
      return $this->render('book/showBooks.html.twig',['book'=>$list]);
    }

    #[Route('/research', name: 'research')]
    public function Research( BookRepository $repo, Request $request)
    {   $book= new Book();
        $form=$this->createForm(ResearchType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            return $this->render('book/showBooks.html.twig', [  'books' => $repo->research($book->getRef()), 'form'=>$form->createView() ]);
        
        }
        return $this->render('book/showBooks.html.twig', [  'books' => $repo->findAll(), 'form'=>$form->createView() ]);
    }
   
}
