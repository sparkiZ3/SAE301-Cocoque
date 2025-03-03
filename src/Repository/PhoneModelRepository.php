<?php

namespace App\Repository;

use App\Entity\PhoneModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository en charge de la gestion des entités PhoneModel.
 * Fournit des méthodes pour manipuler les modèles de téléphones dans la base de données.
 *
 * @extends ServiceEntityRepository<PhoneModel>
 */
class PhoneModelRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository des modèles de téléphone.
     *
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneModel::class);
    }

    /**
     * Récupère un tableau des informations d'un modèle de téléphone par son identifiant.
     *
     * @param string $id : L'identifiant du modèle.
     * @return array : Informations du modèle sous forme de tableau.
     */
    public function getArrayOfModeleBrand(string $id){
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()[0];
    }

    /**
     * Récupère les modèles de téléphone d'une marque donnée.
     *
     * @param string $brand : Le nom de la marque.
     * @return array : Liste des modèles correspondants.
     */
    public function getModeleByBrand(string $brand){
            return $this->createQueryBuilder('p')
                ->andWhere('p.brand = :brand')
                ->setParameter('brand', $brand)
                ->getQuery()
                ->execute();

    }
    
}
