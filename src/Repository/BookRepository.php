<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
      public function ShowAllBooks($value): ?Book
        {
            $req= $this->createQueryBuilder('b')//select b as from book
            // ->where(predicates:'b.title LIKE :param')
            // ->setParameter('param','%a')//pour la param nome
            ->where(predicates:'b.title LIKE ?1')//parametre positionnel
            ->setParameter('1',value:'%a')
            ->orderBy('b.title',order:'ASC')            ->getQuery()
            ->getResult() ;
            return $req;
            }
            
            public function showALLDQL(){
                $em=$this->getEntityManager();
                $list=$em->createQuery('select p from  App//Entity/Book p') ;
                return $list->getResult();
            }
            public function research($ref){
               
                    return $this->createQueryBuilder('b')
                        ->join('b.author','a')
                        ->where('b.ref LIKE :ref')
                        ->setParameter('ref', '%'.$ref.'%') //$ref est la valeur passée en param de la fonction
                        ->getQuery()
                        ->getResult();
                
            }
}
