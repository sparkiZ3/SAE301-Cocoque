<?php

namespace App\Repository;

use App\Entity\Order;
use App\Order\Facturation;
use App\Order\Livraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository en charge de la gestion des entités Order.
 * Fournit des méthodes pour manipuler les commandes dans la base de données.
 *
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository des commandes.
     *
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     * @param EntityManagerInterface $em : Gestionnaire des entités Doctrine.
     */
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em)
    {
        parent::__construct($registry, Order::class);
        $this->em = $em;
    }

    /**
     * Ajoute un produit à une commande.
     *
     * @param int $idUser : Identifiant de l'utilisateur.
     * @param int $idProduct : Identifiant du produit.
     * @param int $quantity : Quantité de produit.
     * @param \DateTimeInterface $orderDate : Date de la commande.
     * @param int $orderNumber : Numéro de la commande.
     */
    public function addProductToOrder(int $idUser,int $idProduct,int $quantity, \DateTimeInterface $orderDate, int $orderNumber): void{
        $order = new Order();
        $order->setProductId($idProduct);
        $order->setCustomerId($idUser);
        $order->setQuantity($quantity);
        $order->setOrderDate($orderDate);
        $order->setOrderNumber($orderNumber);
        $this->em->persist($order);
        $this->em->flush();
    }

     /**
     * Retourne le nombre total d'éléments dans les commandes.
     *
     * @return int : Nombre total d'éléments.
     */
    public function getTotalElements():int{
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère les 20 dernières commandes.
     *
     * @return array : Liste des 20 dernières commandes.
     */
    public function get20Orders(){
        return $this->createQueryBuilder('o')
        ->setMaxResults(20)
        ->getQuery()
        ->getResult();
    }

    /**
     * Recherche des commandes par numéro de commande.
     *
     * @param mixed $search : Le numéro de commande recherché.
     * @return array : Liste des commandes correspondantes.
     */
    public function searchOrderBy($search){
        return $builder = $this->createQueryBuilder('o')
        ->where('o.orderNumber = :search')
        ->setParameter('search', $search)
        ->getQuery()
        ->getResult();
    }

    /**
     * Récupère les commandes associées à un identifiant utilisateur.
     *
     * @param int $id : Identifiant utilisateur.
     * @return array : Liste des commandes de l'utilisateur.
     */
    public function getOrderById(int $id): Array{
        return $this->createQueryBuilder('e')
        ->where('e.customerId = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->getArrayResult();
    }
    /**
     * Récupère le montant total du panier d'un utilisateur, en tenant compte d'un code promo éventuel.
     *
     * Cette méthode appelle une procédure stockée dans la base de données pour calculer le prix total
     * du panier de l'utilisateur, avec la possibilité d'appliquer un code promotionnel.
     * Si un code promo est fourni, il est pris en compte dans le calcul. Sinon, le calcul est effectué 
     * sans promo. La procédure stockée renvoie le prix total calculé.
     * 
     * Si le résultat de la procédure est nul ou si l'utilsateur n'existe pas ou si son panier est vide, la méthode retourne `null`.
     *
     * @param int $user_id L'identifiant de l'utilisateur pour lequel le total est calculé.
     * @param int|null $promo_id L'identifiant du code promo à appliquer, ou `null` si aucun code promo.
     *
     * @return int|null Le prix total calculé pour le panier de l'utilisateur, ou `null` si le calcul échoue.
     */
    public function getTotalAmount(int $user_id, ?int $promo_id = null ) : ?int{
        $totalPrice = 0;
        $connection = $this->em->getConnection();
    
        // Préparation de la requête pour exécuter la procédure stockée
        $sql = 'CALL CALCULER_PRIX_PANIER(:user_id,:promo_id, @totalPrice)'; // Remplacer [procedure] par le nom réel de la procédure
        
        // Préparer la requête
        $stmt = $connection->prepare($sql);
        
        // Lier le paramètre id
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':promo_id', $promo_id);
        
        // Exécuter la requête
        $stmt->execute();
        $result = $stmt->fetch();
        if ( $result["totalPrice"] == null ) {
            return null;
        }
        $totalPrice = $result["totalPrice"];
        return $totalPrice;
    }


    /**
     * Valide le panier et le convertit en commande en appelant une procédure stockée.
     *
     * @param int $id : Identifiant utilisateur.
     * @param int $idAdFactu : Identifiant de l'adresse de facturation.
     * @param int $idAdLivr : Identifiant de l'adresse de livraison.
     */
    public function validateCartToOrder(int $id, int $idAdFactu, int $idAdLivr ){
        $connection = $this->em->getConnection();
    
        // Préparation de la requête pour exécuter la procédure stockée
        $sql = 'CALL CREER_COMMANDE(:id,:idAdFactu,:idAdLivr)'; // Remplacer [procedure] par le nom réel de la procédure
        
        // Préparer la requête
        $stmt = $connection->prepare($sql);
        
        // Lier le paramètre id
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':idAdFactu', $idAdFactu);
        $stmt->bindValue(':idAdLivr', $idAdLivr);
        
        // Exécuter la requête
        $stmt->execute();
        return ;
    }

    /**
     * Récupère les numéros des commandes, avec un maximum de valeurs.
     *
     * @param int $maxvalues : Nombre maximal de résultats.
     * @return array : Liste des numéros de commandes.
     */
    public function getOrderNumbers(int $maxvalues = 20){
        $results = $this->createQueryBuilder('o')
        ->select('o.orderNumber')
        ->distinct()
        ->setMaxResults($maxvalues)
        ->getQuery()
        ->getResult();
        return array_column($results, 'orderNumber');
    }

    /**
     * Donne le prix total d'une commande.
     *
     * @param int $idOrder : Identifiant de la commande.
     * @return float : Prix total de la commande.
     */
    public function calcOrderPrice(int $idOrder){
        $results = $this->createQueryBuilder('o')
        ->select()
        ->where('o.orderNumber = :orderId')
        ->setParameter('orderId',$orderId)
        ->getQuery()
        ->getResult();

        return array_column($results, 'orderNumber');
    }

    /**
     * Factory qui crée une instance de commande (facturation ou livraison).
     *
     * @param string $type : Type de commande ('facturation' ou 'livraison').
     * @param array $adressInfos : Informations sur l'adresse.
     * @return Facturation|Livraison : Instance de la commande créée.
     * @throws \Exception : Si le type est invalide.
     */
    public function orderFactory(string $type, array $adressInfos){
        if($type == "facturation"){
            return new Facturation($adressInfos,);
        }else if($type == "livraison"){
            return new Livraison($adressInfos);
        }else{
            throw new \Exception('impossible de créer l\'élement :'.$type);
        }
    }

    /**
     * Récupère l'identifiant du produit associé à une commande.
     *
     * @param int $idOrder : Identifiant de la commande.
     * @return int : Identifiant du produit.
     */
    public function getProductIdByOrderId(int $idOrder): int{
        $res = $this->createQueryBuilder('e')
        ->select('e.productId')
        ->where('e.id = :identifier')
        ->setParameter('identifier',$idOrder)
        ->getQuery()
        ->getOneOrNullResult();
        return $res['productId'];
    }
}
