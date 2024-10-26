<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
 //Query Builder: Question 2
 public function showAllBooksByRef($ref)
 {
     return $this->createQueryBuilder('b')
         ->join('b.author', 'a')
         ->addSelect('a')
         ->where('b.ref = :ref')
         ->setParameter('ref', $ref)
         ->getQuery()
         ->getResult();
 }
  //Query Builder: Question 3
  public function booksListByAuthors()
  {
      return $this->createQueryBuilder('b')
          ->join('b.author', 'a')
          ->addSelect('a')
          ->orderBy('a.username', 'ASC')
          ->getQuery()
          ->getResult();
  }

   //Query Builder: Question 4
public function showBooksByDateAndNbBooks(int $nbooks, string $year)
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->addSelect('a')
        ->where('a.nbBooks > :nbooks')
        ->andWhere('b.publicationDate < :year')
        ->setParameter('nbooks', $nbooks)
        ->setParameter('year', $year)
        ->getQuery()
        ->getResult();
}
//Query Builder: Question 5
public function updateBooksCategoryByAuthor($category)
{
    return $this->getEntityManager()->createQueryBuilder()
        ->update('App\Entity\Book', 'b')
        ->set('b.category', ':newCategory')
        ->setParameter('newCategory', 'Romance') 
        ->where('b.category = :oldCategory')
        ->setParameter('oldCategory', $category)
        ->getQuery()
        ->execute(); 
}
//fct 
public function findByPublicationDateRange($startDate, $endDate)
{
    return $this->createQueryBuilder('b')
        ->where('b.publicationDate >= :start')
        ->andWhere('b.publicationDate <= :end')
        ->setParameter('start', $startDate)
        ->setParameter('end', $endDate)
        ->getQuery()
        ->getResult();
}

}
