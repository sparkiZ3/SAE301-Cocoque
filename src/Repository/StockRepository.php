<?php

namespace App\Repository;

use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository en charge de la gestion des entités Stock.
 * Fournit des méthodes pour manipuler les stocks dans la base de données.
 *
 * @extends ServiceEntityRepository<Stock>
 */
class StockRepository extends ServiceEntityRepository
{
     /**
     * Constructeur du repository de stocks.
     *
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }
    /**
     * Met à jour la quantité en stock d'un produit spécifique.
     * Si la quantité devient inférieure ou égale à zéro, une exception est lancée pour indiquer que le stock est vide.
     *
     * @param int $id : L'identifiant du produit dont la quantité en stock doit être mise à jour.
     * @param int $quantity : La quantité à mofifier dans le stock du produit.
     *
     * @throws \Exception Si la quantité du stock devient inférieure ou égale à zéro.
     */
    public function updateQuantityStock(int $id, int $quantity): void{
        $qte = $this->getQuantityOfElement($id)+$quantity; //on recupere la quantité actuelle et on ajoute 1 ou -1
        if($qte <= 0){
            throw new \Exception('Le stock est vide :'.$id);
        }
        $this->createQueryBuilder('s')
        ->update()
        ->set('s.quantity', $qte)
        ->where('s.id = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->execute();
    }
    /**
     * Récupère la quantité d'un produit spécifique dans le stock.
     *
     * @param int $id : L'identifiant du produit dont on veut connaître la quantité en stock.
     *
     * @return int|null : La quantité du produit dans le stock, ou `null` si le produit n'existe pas.
     */
    public function getQuantityOfElement(int $id):?int{ //donne le la quantité d'un produit present dans le stock définie par son id
        $result = $this->createQueryBuilder('s')
        ->select('s.quantity')
        ->where('s.id = :identifier')
        ->setParameter('identifier', $id)
        ->getQuery()
        ->getOneOrNullResult();

        return $result ? $result['quantity'] : null;
    }
}
