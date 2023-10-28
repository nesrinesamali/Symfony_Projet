<?php

namespace App\Controller;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;






class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/showauthor/{name}', name: 'show_author')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig',['name'=>$name]);

    }

    #[Route('/listauthors', name:'list_authors')]
    public function list(): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>'taha.hussein@gmail.com', 'nb_books' => 300),
            );

        return $this->render('author/list.html.twig', ['authors' => $authors]);
    }

    #[Route('/auhtorDetails/{id}', name:'auhtorDetails')]
    public function auhtorDetails($id): Response
    {
        return $this->render('author/showAuthor.html.twig', ['authors'=>$id]);
    }


    #[Route('/listAuthor', name:'list_author')]
    public function listAuthor(AuthorRepository $authorrepository): Response
    {
        $list=$authorrepository->findAll();
        return $this->render('author/listAuthor.html.twig', ['authors'=>$list]);
    }

    


    #[Route('/AjoutStatique', name: 'author_ajoutStatique')]
    public function ajoutStatique(EntityManagerInterface $entityManager): Response
    {
        
        $author1 = new Author();
        $author1->setUsername("Molière");
        $author1->setEmail("Molière@gmail.com"); 

        $entityManager->persist($author1);
        $entityManager->flush();

        return $this->redirectToRoute('list_author');
    }
    #[Route('/Ajout', name: 'author_ajout')]

    public function  Ajout (Request  $request)
    {
        $author=new Author();
        $form =$this->CreateForm(AuthorType::class,$author);
        $form->add('Save',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('list_author');
        }
        return $this->render('author/ajout.html.twig',['form'=>$form->createView()]);
    
    }
    #[Route('/edit/{id}', name: 'author_edit')]
    public function modifier(AuthorRepository $repository, $id, Request $request)
    {
        $author = $repository->find($id);
        $form = $this->createForm(AuthorType::class, $author);
        $form->add('Edit', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush(); 
            return $this->redirectToRoute("list_author");
        }

        return $this->render('author/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

   
    #[Route('/delete/{id}', name: 'author_delete')]
public function deleteAuthor(Request $request, $id, ManagerRegistry $manager): Response
{
    $em = $manager->getManager();
    $authorRepository = $em->getRepository(Author::class);

    $author = $authorRepository->find($id);

   
    if ($author !== null) {
      
        $em->remove($author);
        $em->flush();

        $list = $authorRepository->findAll();

        return $this->render('author/listAuthor.html.twig', ['authors' => $list]);
    } else {
       
        return new Response('Auteur non trouvé', Response::HTTP_NOT_FOUND);
    }
}




#[Route('/deleteAuthorsnbzero', name: 'deleteAuthorsnbzero')]
    public function deleteAuthorsnbzero(ManagerRegistry $manager, AuthorRepository $authorRepository)
    {
        $list = $authorRepository->findBy(['nb_books' => 0]);
        $em=$manager->getManager();
        foreach ($list as $author) {
            $em->remove($author);
        }
        $em->flush();

        
        return $this->redirectToRoute('list_author');
    }

}

