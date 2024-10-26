<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository; 


class AuthorController extends AbstractController
{    

  
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    { 
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);

    }
    #[Route('/showAuthor/{name}', name: 'app_showAuthor')]
    public function showAuthor ($name) {
        return $this->render('author/show.html.twig',[ 'n'=>$name ]);

    }
    #[Route('/showlist', name: 'app_showlist')]
    public function list (){
   
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
            return $this->render("/author/list.html.twig",['authors'=>$authors]);
    }

    #[Route('/author/details/{id}', name: 'app_author_details')]
public function authorDetails($id) {
   
    $authors = array(
        array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),
        array('id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
        array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
    );

    $author = null;
    foreach ($authors as $a) {
        if ($a['id'] == $id) {
            $author = $a;
            break;
        }
    }

    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    return $this->render('author/showAuthor.html.twig', ['author' => $author]);
}


//Affiche
#[Route('/Affiche', name: 'app_Affiche')]
public function Affiche (AuthorRepository $repo)
{
    $author=$repo->findAll(); //select*
    return $this->render('author/Affiche.html.twig',['author'=>$author]);
}

// addStatique
#[Route('/AddStatique', name: 'app_ AddStatique')]
public function addStatique(ManagerRegistry $manager){

    $em= $manager->getManager();
    $author1=new Author();
    $author1->setUsername("nour");
    $author1->setEmail("nour@gmail.com");
    
    //ajout 

    $em->persist($author1);
    $em->flush();
    return $this->redirectToRoute("app_Affiche");
}


//add


#[Route('/Add', name: 'app_Add')]
public function Add(Request $request,ManagerRegistry $manager)
{
    $em= $manager->getManager();
    $author = new Author();
    $form = $this->createForm(AuthorType::class, $author);
    $form->add('Ajouter', SubmitType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('app_Affiche');
    }

    return $this->render('author/Add.html.twig', [
        'form' => $form->createView(),
    ]);  
}

//edit

#[Route('/edit/{id}', name: 'app_edit')]
public function edit(AuthorRepository $repository, $id, Request $request, EntityManagerInterface $em): Response
{
    $author = $repository->find($id);
    if (!$author) {
        throw $this->createNotFoundException('Author not found');
    }

    $form = $this->createForm(AuthorType::class, $author);
    $form->add('update', SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('app_Affiche');
    }
    return $this->render('author/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
 
//delete
#[Route('/delete/{id}', name: 'app_delete')]
public function delete(ManagerRegistry $manager, AuthorRepository $repo, $id)
{
    $author = $repo->find($id);
    if (!$author) {
        throw $this->createNotFoundException('Author not found');
    }

    $em = $manager->getManager();
    $em->remove($author);
    $em->flush();

    return $this->redirectToRoute('app_Affiche');
}

 //Query Builder: Question 1
 #[Route('/author/list/OrderByEmail', name: 'app_author_list_ordered', methods: ['GET'])]
public function listAuthorByEmail(AuthorRepository $authorRepository): Response
{
    return $this->render('author/orderedList.html.twig', [
        'authors' => $authorRepository->listAuthorByEmail(),
    ]);
}

  


}
