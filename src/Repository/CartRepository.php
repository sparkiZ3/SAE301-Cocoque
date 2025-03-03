<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository en charge de la gestion des entités Cart.
 * Fournit des méthodes pour manipuler le panier d'achats des utilisateurs.
 *
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository du panier.
     *
     * @param ManagerRegistry $registry : Gestionnaire d'entitées Doctrine.
     * @param EntityManagerInterface $em : Gestionnaire des entités pour effectuer les persistances et flush.
     */
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em)
    {
        parent::__construct($registry, Cart::class);
        $this->em = $em;
    }
    /**
     * Récupère la taille du panier d'un utilisateur.
     *
     * @param User|null $user : L'utilisateur dont on souhaite connaître la taille du panier.
     * @return int : Le nombre d'éléments dans le panier.
     */
    public function cartsizeByUser(?User $user): int{ //on passe par le user car on peux verfifier si il est null car sinon on ne peux pas appliquer la fonction getId() dans le main controller sur un null
        if($user != null){
            $res = $this->createQueryBuilder('e')
            ->where('e.customerId = :identifier')
            ->setParameter('identifier',$user->getId())
            ->getQuery()
            ->getArrayResult();
            return count($res);
        }else{
            return 0;
        }
    }
     /**
     * Récupère les éléments du panier pour un utilisateur donné.
     *
     * @param int $id : L'identifiant de l'utilisateur.
     * @return array : Les éléments présents dans le panier.
     */
    public function getCartElementsById(int $id): Array{
        return $this->createQueryBuilder('e')
        ->where('e.customerId = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->getArrayResult();
    }

    /**
     * Récupère la quantité d'un produit présent dans le panier via son identifiant.
     *
     * @param int $id : L'identifiant de l'élément dans le panier.
     * @return int : La quantité du produit.
     */
    public function getQuantityOfElement(int $id):int{ //donne le la quantité d'un produit present dans le panier définir par son id
        $res = $this->createQueryBuilder('e')
        ->select('e.quantity')
        ->where('e.id = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->getArrayResult();
        return $res[0]['quantity'];
    }

    /**
     * Met à jour la quantité d'un produit dans le panier.
     *
     * Si la quantité tombe à zéro ou en-dessous, l'élément est supprimé du panier.
     *
     * @param int $id : L'identifiant de l'élément dans le panier.
     * @param int $quantity : La quantité à ajouter ou retirer.
     */
    public function updateQuantityCart(int $id, int $quantity): void{
        $qte = $this->getQuantityOfElement($id)+$quantity; //on recupere la quantité actuelle et on ajoute 1 ou -1
        if($qte <= 0){
            $this->removeCartElement($id);
        }
        
        $this->createQueryBuilder('e')
        ->update()
        ->set('e.quantity', $qte)
        ->where('e.id = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->execute();
    }

    /**
     * Supprime un élément du panier.
     *
     * @param int $id : L'identifiant de l'élément dans le panier.
     */
    public function removeCartElement(int $id): void{
        $this->createQueryBuilder('e')
        ->delete()
        ->where('e.id = :identifier')
        ->setParameter('identifier',$id)
        ->getQuery()
        ->execute();
    }
    

    /**
     * Ajoute un produit au panier.
     *
     * @param int $idUser : L'identifiant de l'utilisateur.
     * @param int $idProduct : L'identifiant du produit.
     * @param int $quantity : La quantité à ajouter.
     */
    public function addProductToCart(int $idUser,int $idProduct,int $quantity): void{
        $cart = new Cart();
        $cart->setProductId($idProduct);
        $cart->setCustomerId($idUser);
        $cart->setQuantity($quantity);
        $this->em->persist($cart);
        $this->em->flush();
    }
    /**
     * Supprime tous les éléments du panier pour un utilisateur donné.
     *
     * @param int $idUser : L'identifiant de l'utilisateur.
     */
    public function removeAll(int $idUser):void{
        $this-> createQueryBuilder('u')
        ->delete()
        ->where('u.customerId = :idUser')
        ->setParameter('idUser', $idUser)
        ->getQuery()
        ->execute();
    }
    /**
     * Récupère l'identifiant du produit correspondant à l' identifiant du panier.
     *
     * @param int $idCart : L'identifiant du panier.
     * @return int : L'identifiant du produit.
     */
    public function getProductIdByCartId(int $idCart): int{
        $res = $this->createQueryBuilder('e')
        ->select('e.productId')
        ->where('e.id = :identifier')
        ->setParameter('identifier',$idCart)
        ->getQuery()
        ->getOneOrNullResult();
        return $res['productId'];
    }
}
