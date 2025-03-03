<?php 
namespace App\Controller;


use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use App\Repository\CollectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

class MainController extends AbstractController{

    /**
     * Affiche la page d'accueil avec les collections et les produits tendance.
     *
     * Les paramètres sont (pagination: pour gérer l'affichage des collections en plusieurs pages, cartCount: Le nombre d'éléments dans le panier, trendyCases: les produits tendance sélectionnés par leur ID).
     *
     * @param Request $request : La requête HTTP contenant les paramètres de pagination.
     * @param ProductRepository $productRepo : Le repository permettant d'accéder aux infos des produits.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param CollectionsRepository $collectionRepo : Le repository permettant d'accéder aux infos des collections de produits.
     * @param PaginatorInterface $paginator : L'interface pour gérer la pagination des collections.
     * @return Response La réponse contenant la page d'accueil avec les produits tendance et les collections.
     */
    #[Route('/', name : 'app_home')]
    public function homePage(Request $request,ProductRepository $productRepo,CartRepository $cartRepo,CollectionsRepository $collectionRepo,PaginatorInterface $paginator){
        $trendyCasesId = [4,5,6,3];
        $trendyProducts =[];
        $collectionList = $collectionRepo->getCollections();
        $collectionCount = $collectionRepo->countCollections();
        $pagination = $paginator->paginate($collectionList,$request->query->getInt('page', 1), $collectionCount);
        foreach($trendyCasesId as $trendyCase){
            $product = $productRepo->findOneBy(['id'=> $trendyCase]);
            $trendyProducts[]= $product;
        }
        return $this->render('main/homepage.html.twig',[
            'pagination' => $pagination,
            'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
            'trendyCases'=>$trendyProducts,
        ]);
    }
}