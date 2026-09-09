<?php

namespace App\Repository;

use App\Entity\Advertisement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Advertisement>
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }
    public function findAllWithLikes():array
    {
        return $this->findAllQuery(withFavorites: true)->getQuery()->getResult();
    }

    public function findAllByAuthor(int | User $author, ?string $search = null,?string $location = null,string $sort = 'newest',):array
    {
        return $this->findAllQuery(
            withFavorites:true,
            withAuthors:true,
            withProfiles:true,
            withImages: true,
            search:$search,
            location:$location,
            sort:$sort,
            
        )->where('a.author = :author')
        ->setParameter(
            'author',
            $author instanceof User ? $author->getId() : $author
        )->getQuery()->getResult();
    }
    public function findAllWithMinFavorites(int $minFavorites):array{
        
        $idList = $this->findAllQuery(
            withFavorites:true,
        )->select('a.id')
        ->groupBy('a.id')
        ->having('COUNT(f)>= :minFavorites')
        ->setParameter('minFavorites',$minFavorites)
        ->getQuery()->getResult(Query::HYDRATE_SCALAR_COLUMN);

        return $this->findAllQuery(
            withAuthors:true,
            withFavorites:true,
            withProfiles:true
        )->where('a.id in (:idList)')
        ->setParameter('idList',$idList)
        ->getQuery()->getResult();
    }

    public function sortByMostFavorites(int $minFavorites=1, ?int $limit=null):array{
        $qb=$this->createQueryBuilder('a')
        ->leftJoin('a.favoritedBy','f')
        ->addSelect('COUNT(f.id) AS HIDDEN favCount')
        ->groupBy('a.id')
        ->having('COUNT(f.id) >= :min')
        ->setParameter('min',$minFavorites)
        ->orderBy('favCount','DESC');
        if($limit){
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getResult();
    }

    public function findAllQuery(
        bool $withFavorites=false,
        bool $withAuthors=false,
        bool $withProfiles=false,
        bool $withImages = false,
        ?string $search = null,
        ?string $location = null,
        string $sort = 'newest',
        ) : QueryBuilder{
        $query = $this->createQueryBuilder('a');

        if ($withFavorites) {
            $query->leftJoin('a.favoritedBy', 'f')
                ->addSelect('f')
                ->leftJoin('f.userProfile', 'fp')
                ->addSelect('fp');
        }

        if ($withAuthors || $withProfiles) {
            $query->leftJoin('a.author', 'au')
                ->addSelect('au');
        }

        if ($withProfiles) {
            $query->leftJoin('au.userProfile', 'up')
                ->addSelect('up');
        }

        if ($withImages) {
            $query->leftJoin('a.images', 'i')->addSelect('i');
        }

        if ($search) {
            $query->andWhere('(a.title LIKE :search OR a.description LIKE :search)')
            ->setParameter('search', '%' . addcslashes($search, '%_') . '%');
        }

        if ($location) {
            $query->andWhere('a.locationName LIKE :location')
                ->setParameter('location', '%' . addcslashes($location, '%_') . '%');
        }
        return match ($sort) {
            'oldest' => $query->orderBy('a.created', 'ASC'),
            default  => $query->orderBy('a.created', 'DESC'),
        };

        return $query->orderBy('a.created', 'DESC');
    }
    public function findAllPaginated(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $location = null,
        string $sort = 'newest',): Paginator
    {
        $query = $this->findAllQuery(
            withFavorites:true,
            withAuthors:true,
            withProfiles:true,
            withImages:true,
            search:$search,
            location:$location,
            sort:$sort,
            
        )
        ->getQuery()
        ->setFirstResult(($page - 1) * $perPage)
        ->setMaxResults($perPage);

        return new Paginator($query, fetchJoinCollection: true);
    }

    //    /**
    //     * @return Advertisement[] Returns an array of Advertisement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advertisement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
