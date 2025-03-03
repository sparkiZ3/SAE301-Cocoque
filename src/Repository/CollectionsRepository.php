<?php

namespace App\Repository;

use App\Entity\Collections;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository en charge de la gestion des entités Collections.
 * Fournit des méthodes pour manipuler les collections dans la base de données.
 *
 * @extends ServiceEntityRepository<Collections>
 */
class CollectionsRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository des collections.
     *
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collections::class);
    }

    /**
     * Récupère toutes les collections.
     *
     * @return array : Liste des collections.
     */
    public function getCollections(){
        return $this->createQueryBuilder('c')
        ->getQuery()
        ->getResult();
    }

    /**
     * Recherche des collections par identifiant ou nom.
     *
     * @param mixed $search : Le critère de recherche (identifiant ou nom partiel).
     * @return array : Liste des collections correspondantes.
     */
    public function searchCollecBy($search){
        return $builder = $this->createQueryBuilder('c')
        ->where('c.id = :searchId')
        ->orWhere('c.collectionName LIKE :search')
        ->setParameter('search', '%'.$search.'%')
        ->setParameter('searchId',$search)
        ->getQuery()
        ->getResult();
    }

    /**
     * Supprime une collection par son identifiant.
     *
     * @param int $id : L'identifiant de la collection à supprimer.
     */
    public function deleteCollec(int $id){
        $this->createQueryBuilder('c')
        ->delete()
        ->where('c.id = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->execute();
    }

    /**
     * Compte le nombre total de collections.
     *
     * @return int : Le nombre total de collections.
     */
    public function countCollections(): int{
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}
