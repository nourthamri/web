<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;  // Import Request
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;
use App\Entity\Book;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;  // Import SubmitType
use App\Form\BookType;  // Assuming you have created a BookType form
use Doctrine\ORM\EntityManagerInterface;
use App\Form\SearchBookType;
use App\Form\BookFilterType;

 // Make sure this is the correct import



class BookController extends AbstractController
{
    #[Route('/AddBook', name: 'app_AddBook')]
    public function Add(Request $request, ManagerRegistry $manager)
    {
        $em = $manager->getManager();
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assuming you are dealing with an Author entity somewhere
            $book->setEnabled(true);  // Assuming the Book entity has an "enabled" field

            // If you have an Author instance, update the number of books
            $author = $book->getAuthor();  // Assuming book has an author field
            if ($author instanceof Author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }

            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('app_AfficheBook');
        }

        return $this->render('book/Add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/Afficher', name: 'app_AfficheBook')]
    public function Affiche(ManagerRegistry $manager)
    {
        // Use the injected ManagerRegistry to get the repository
        $enabledBooks = $manager->getRepository(Book::class)->findBy(['enabled' => true]);

        // Count the number of published and unpublished books
        $numEnabled = count($enabledBooks);
        $numDisabled = count($manager->getRepository(Book::class)->findBy(['enabled' => false]));

        if ($numEnabled > 0) {
            return $this->render('book/Afficher.html.twig', [
                'enabledBooks' => $enabledBooks,
                'numEnabled' => $numEnabled,
                'numDisabled' => $numDisabled,
            ]);
        } else {
            // Display message if no books are found
            return $this->render('book/nb_books_found.html.twig');
        }
    }

    #[Route('/editBook/{ref}', name: 'app_editBooks')]
    
    public function edit(BookRepository $repository, EntityManagerInterface $entityManager, $ref, Request $request): Response
    {
        // Find the book by its reference number
        $book = $repository->find($ref);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }
    
        // Create the form for the book entity
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated book entity
            $entityManager->persist($book);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_AfficheBook');
        }
    
        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/deleteBook/{ref}', name: 'app_deleteBooks')]
    public function delete(BookRepository $repository, EntityManagerInterface $entityManager, $ref): Response
    {
        // Find the book by its reference number
        $book = $repository->find($ref);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }
    
        // Before removing the book, ensure to handle the foreign key constraints
        // Check if the book has an associated author and decrement their book count if needed
        $author = $book->getAuthor();
        if ($author instanceof Author) {
            $author->setNbBooks($author->getNbBooks() - 1);
            // Only remove the author if they no longer have any books
            if ($author->getNbBooks() <= 0) {
                $entityManager->remove($author);
            }
        }
    
        // Remove the book entity
        $entityManager->remove($book);
        $entityManager->flush();
    
        // Redirect to the book list after deletion
        return $this->redirectToRoute('app_AfficheBook');
    }

    #[Route('/ShowBook/{ref}', name: 'app_detailsBook')]


    public function showBook($ref, BookRepository $rep)
    { 
        $book=$rep->find($ref);
        if(!$book)
        {
            return $this->redirectToRoute('app_AfficheBook');
        }
        return $this->render('book/show.html.twig',['b'=>$book]);
    }


    //Query Builder: Question 2
    #[Route('/book/list/search', name: 'app_book_search', methods: ['GET', 'POST'])]
    public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(SearchBookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            return $this->render('book/listSearch.html.twig', [
                'books' => $bookRepository->showAllBooksByRef($book->getRef()),
                'f' => $form->createView()
            ]);
        }
        return $this->render('book/listSearch.html.twig', [
            'books' => $bookRepository->findAll(),
            'f' => $form->createView()
        ]);
    } 
     //Query Builder: Question 3
// #[Route('/book/list/author', name: 'app_book_list_author', methods: ['GET'])]
// public function showOrderedBooksByAuthor(BookRepository $bookRepository): Response
// {
//     return $this->render('book/listBookAuthor.html.twig', [
//         'books' => $bookRepository->booksListByAuthors(),
//     ]);
// }
#[Route('/book/list/author', name: 'app_book_list_author', methods: ['GET'])]
public function showOrderedBooksByAuthor(BookRepository $bookRepository): Response
{
    $books = $bookRepository->booksListByAuthors(); // Fetching the books
    
    // Debugging: Check if books are empty
    if (empty($books)) {
        throw new \Exception('No books found or booksListByAuthors is not returning anything.');
    }

    return $this->render('book/listBookAuthor.html.twig', [
        'books' => $books, // Passing the books to the template
    ]);
}
//Query Builder: Question 4
#[Route('/book/list/QB', name: 'app_book_list_author_date', methods: ['GET'])]
public function showBooksByDateAndNbBooks(BookRepository $bookRepository): Response
{
    return $this->render('book/listBookDateNbBooks.html.twig', [
        'books' => $bookRepository->showBooksByDateAndNbBooks(10, '2023-01-01'),
    ]);
}
//QueryBuilder :Question 5 
#[Route('/book/list/author/update/{category}', name: 'app_book_list_author_update', methods: ['GET'])]
public function updateBooksCategoryByAuthor($category, BookRepository $bookRepository): Response
{
    $bookRepository->updateBooksCategoryByAuthor($category);
    return $this->render('book/listBookAuthor.html.twig', [
        'books' => $bookRepository->findAll(),
    ]);
}

//fct 
#[Route('/books/filter', name: 'app_book_filter')]
public function filter(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(BookFilterType::class);
    $form->handleRequest($request);

    $books = [];

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        $books = $em->getRepository(Book::class)->findByPublicationDateRange($startDate, $endDate);
    }

    return $this->render('book/filter.html.twig', [
        'form' => $form->createView(),
        'books' => $books,
    ]);
}

// {
//     $form = $this->createForm(BookFilterType::class);
//     $form->handleRequest($request);

//     $books = [];

//     if ($form->isSubmitted() && $form->isValid()) {
//         $data = $form->getData();
//         $startDate = $data['startDate'];
//         $endDate = $data['endDate'];

//         if ($startDate && $endDate) {
//             // Query to filter books by date range
//             $books = $em->getRepository(Book::class)->createQueryBuilder('b')
//                 ->where('b.publicationDate >= :start')
//                 ->andWhere('b.publicationDate <= :end')
//                 ->setParameter('start', $startDate)
//                 ->setParameter('end', $endDate)
//                 ->getQuery()
//                 ->getResult();
//         }
//     }

//     return $this->render('book/filter.html.twig', [
//         'form' => $form->createView(),
//         'books' => $books,
//     ]);
// }




    
    

    
    
}
