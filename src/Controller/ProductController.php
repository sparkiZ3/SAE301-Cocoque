<?php 

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\PhoneModelRepository;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

class ProductController extends AbstractController{


    /**
     * Affiche tous les produits avec options de tri et de filtrage par prix.
     *
     * Les paramètres sont (pagination: pour gérer l'affichage en plusieurs pages des résultats, cartCount: Le nombre d'éléments dans le panier, direction: direction du tri (croissnt ou decroissant), priceMin et priceMax: les limites de prix).
     *
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param ProductRepository $productRepo : Le repository permettant d'accéder aux infos des produits.
     * @param PaginatorInterface $paginator : L'interface pour gérer la pagination.
     * @param Request $request : La requête contenant les paramètres de tri et de filtrage.
     * @return Response La réponse contenant la page avec les produits affichés.
     */
    #[Route('/products', name: 'showAllProducts')]
    public function index(CartRepository $cartRepo,ProductRepository $productRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $direction = $request->query->get('direction');
        $priceMin = $request->query->get('priceMin');
        $priceMax = $request->query->get('priceMax');
        //valeur par défaut
        if($priceMin == null || $priceMax == null){
            $priceMin = 0;
            $priceMax = 100;
        }
        if($direction !=null){
            $productList = $productRepo->sortByPrice($direction, $priceMin,$priceMax);
        }
        else{
            $productList = $productRepo->findAll();
        }
        $totalResult=count($productList);
        $pagination = $paginator->paginate($productList,$request->query->getInt('page', 1),6);
        
        return $this->render('product/allProducts.html.twig', [
            'pagination' => $pagination,
            'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
            'research'=>"",
            'direction'=>$direction,
            'priceMin'=>$priceMin,
            'priceMax'=>$priceMax,
            'totalResult'=>$totalResult,
        ]);
    }

    /**
     * Recherche des produits en fonction de mots-clés.
     *
     * Les paramètres sont (search: le terme de recherche, pagination: pour gérer l'affichage en plusieurs pages des résultats, cartCount: Le nombre d'éléments dans le panier).
     *
     * @param Request $req : La requête contenant les paramètres de recherche.
     * @param PaginatorInterface $paginator : L'interface pour gérer la pagination.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param ProductRepository $productRepo : Le repository permettant d'accéder aux infos des produits.
     * @return Response La réponse contenant la page des résultats ou une redirection si aucun produit n'est trouvé.
     */
    #[Route('/products/search', name: 'showAllProducts_search')]
    public function search(Request $req,PaginatorInterface $paginator ,CartRepository $cartRepo,ProductRepository $productRepo): Response
    {
        $search = $req->query->get('search');

        $searchSplit = explode(" ", $search);


        $productList = $productRepo->getProductsByLabels($searchSplit);
        $totalResult= count($productList);

        if(count($productList)>0){
            $pagination = $paginator->paginate($productList,$req->query->getInt('page', 1),6);

            return $this->render('product/allProducts.html.twig', [
                'pagination' => $pagination,
                'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
                'research'=>$search,
                'direction'=>"",
                'priceMin'=>0,
                'priceMax'=>100,
                'totalResult'=>$totalResult,
            ]);
        }else{
            return $this->redirectToRoute('showAllProducts');
        }

        
    }
    /**
     * Affiche tous les produits.
     *
     * Sert à afficher la liste des produits
     * 
     * @param Request $request : La requête.
     * @param ProductRepository $repo : Le repository des produits.
     * @return Response La réponse contenant la page de tous les produits .
     */
    public function showAll(Request $request, ProductRepository $repo): Response{
        $products = $repo->findAll();
        return $this->render("product/allProducts.html.twig");
    }

     /**
     * Affiche un produit en fonction de son id.
     *
     * Les paramètres sont (product: les infos sur le produit sélectionné, 
     * cartCount: Le nombre d'éléments dans le panier, samsungPhone et applePhone: Les modèles de téléphone, collectionProducts: Les produits de la même collection).
     *
     * @param Request $request : La requête.
     * @param int $id : L'identifiant du produit à afficher.
     * @param ProductRepository $productRepo : Le repository des produits.
     * @param PhoneModelRepository $phoneModelRepo : Le repository des modèles de téléphones.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @return Response La réponse contenant la page du produit.
     */
    #[Route('/product/{id}', name: 'product_show')]
    public function showById(Request $request, int $id,ProductRepository $productRepo,PhoneModelRepository $phoneModelRepo,CartRepository $cartRepo,StockRepository $stockRepo): Response{
        $product = $productRepo->findOneBy(['id'=>$id]);
        if($product){
            $samsungPhone = $phoneModelRepo->getModeleByBrand('samsung');
            $applePhone = $phoneModelRepo->getModeleByBrand('apple');
            $collectionProducts = $productRepo->getProductFromCollection($product->getCollectionId(),$id);
            $remainingProducts = $stockRepo->getQuantityOfElement($id);
            return $this->render('product/productPage.html.twig',[
                    'product'=>$product,
                    'rating'=>3,
                    'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
                    'samsungPhone' => $samsungPhone,
                    'applePhone'=>$applePhone,
                    'collectionProducts'=>$collectionProducts,
                    'stock'=>$remainingProducts,
                ]);
        }else{
            return new Response('produit introuvable',404);
        }
    }
        
}