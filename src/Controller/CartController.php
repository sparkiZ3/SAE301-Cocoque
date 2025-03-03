<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;


#[isGranted('ROLE_USER')]
class CartController extends AbstractController
{
    /**
     * Affiche le contenu du panier de l'utilisateur.
     *
     * Récupère les éléments du panier de l'utilisateur à partir de la base de données,
     * calcule le prix total du panier, le coût de la livraison, et affiche 
     * la page du panier avec ces informations.
     * 
     * @param Request $req La requête HTTP contenant les informations de session, y compris les erreurs.
     * @param CartRepository $cartRepo Le repository utilisé pour récupérer les éléments du panier de l'utilisateur.
     * @param ProductRepository $productRepo Le repository utilisé pour récupérer les informations des produits.
     *
     * @return Response La réponse contenant le rendu de la page du panier avec les informations calculées.
     */
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $req,CartRepository $cartRepo,ProductRepository $productRepo, StockRepository $stockRepo): Response
    {
        $user= $this->getUser();
        $cartElements = $cartRepo->getCartElementsById($user->getId());
        $totalCartPrice = 0;
        $allElements =[];
        foreach( $cartElements as $cartElement ){
            $el['product'] = $productRepo->getOneProductArrayById($cartElement['productId']);
            $el['quantity'] = $cartElement['quantity'];
            $el['id'] = $cartElement['id'];
            $totalCartPrice += $el['product']['unitPrice']*$el['quantity'];
            $allElements[] = $el;
        }
        if ($totalCartPrice>50){
            $deliveryPrice=0;
        }else{
            $deliveryPrice=$totalCartPrice*0.10;
        }
        $session=$req->getSession();
        $session->set('totalAmount',$totalCartPrice+$deliveryPrice);
        return $this->render('cart/index.html.twig', [
            'cartCount'=>$cartRepo-> cartsizeByUser($user),
            'totalCartPrice'=>$totalCartPrice,
            'deliveryPrice'=>$deliveryPrice,
            'cartElements' => $allElements,
        ]);
    }

    /**
     * Met à jour les éléments du panier (ajout, retrait ou suppression).
     *
     * Cette action permet de modifier la quantité d'un produit dans le panier, 
     * de retirer un produit du panier ou supprimer tous les produits du panier en fonction 
     * des paramètres reçus. Elle vérifie également la disponibilité des produits en stock 
     * avant de modifier la quantite d'un produit au panier.
     *
     * @Route('/cart/update', name: 'update_cart')
     *
     * @param Request $req La requête HTTP contenant les informations des actions à effectuer (ajout, retrait, suppression).
     * @param CartRepository $cartRepo Le repository utilisé pour récupérer et mettre à jour les éléments du panier.
     * @param StockRepository $stockRepo Le repository utilisé pour vérifier la quantité des produits en stock.
     *
     * @return Response La réponse qui redirige l'utilisateur vers la page du panier après la mise à jour.
     */
    #[Route('/cart/update', name: 'update_cart')]
    public function update(Request $req,CartRepository $cartRepo, StockRepository $stockRepo): Response
    {
        $addId = $req->request->get('addId');
        
        $decId = $req->request->get('decId');

        $delId = $req->request->get('delId');
        try{
            if($addId != null){
                if ($cartRepo->getQuantityOfElement($addId) < $stockRepo->getQuantityOfElement($cartRepo->getProductIdByCartId($addId)))
                    $cartRepo->updateQuantityCart($addId, 1);
            }else if ($decId != null){
                $cartRepo->updateQuantityCart($decId, -1);
            }else if($delId != null){
                $cartRepo->removeCartElement($delId);
            }
        }catch(\Exception$e){
            if ( $e->getCode() == 20002 ){
                $this->redirectToRoute("app_cart");
            } else if ( $e->getCode() == 2000) {
                $this->redirectToRoute("app_cart");
            }
        }
        return $this->redirectToRoute('app_cart');
    }


    /**
     * Ajoute un produit au panier de l'utilisateur.
     *
     * 
     * Ajoute un produit au panier de l'utilisateur en fonction de la quantité.
     * Verifie si la quantité n'est pas inférieur à 1 ni supérieur à 10
     * ni supérieur à la quantité restante du produit.
     *
     *
     * @param Request $req La requête HTTP contenant les informations sur la quantité et le produit à ajouter.
     * @param CartRepository $cartRepo Le repository utilisé pour ajouter le produit au panier.
     * @param StockRepository $stockRepo Le repository utilisé pour vérifier la quantité du produit ajouté en stock.
     *
     * @return Response La réponse redirige l'utilisateur vers la page du panier après l'ajout.
     */
    #[Route('/cart/add', name: 'add_cart')]
    public function add(Request $req, CartRepository $cartRepo, StockRepository $stockRepo): Response
    {
        $qte = $req->request->get('quantity');

        $productId = $req->request->get('productId');
        $productQuantity = $stockRepo->getQuantityOfElement($productId);
        if ( $productQuantity == null ){
            return $this->redirectToRoute('app_home');
        } else if ( $qte > $productQuantity && ($qte > 10  || $qte < 1)) {
            return $this->redirectToRoute("product_show",["id" => $productId]);
        }

        $cartRepo->addProductToCart($this->getUser()->getId(),$productId,$qte);

        return $this->redirectToRoute('app_cart');
    }


    /**
     * Supprime tous les éléments du panier de l'utilisateur.
     *
     * Permet de vider le panier de l'utilisateur en supprimant 
     * tous les produits qui y sont associés. Après avoir supprimé les éléments 
     * du panier, l'utilisateur est redirigé vers la page du panier.
     *
     *
     * @param CartRepository $cartRepo Le repository utilisé pour gérer les éléments du panier.
     *
     * @return Response La réponse redirige l'utilisateur vers la page du panier après la suppression.
     */
    #[Route('/cart/delete', name: 'delete_cart')]
    public function delete(CartRepository $cartRepo){
        $cartRepo->removeAll($this->getUser()->getId());
        return $this->redirectToRoute('app_cart');
    }
}