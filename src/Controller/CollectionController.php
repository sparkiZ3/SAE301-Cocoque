<?php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\CollectionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

//Controleur en charge des collection 
class CollectionController extends AbstractController
{
    /**
     * Présente la page de collection.
     *
     * Les paramètres sont (pagination: pour gérer l'affichage en plusieurs pages des résultats, cartCount: Le nombre d'éléments dans le panier
     * , research: qui sert à effectuer des recherche par thème).
     *
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param CollectionsRepository $collectionRepo : Le repositry de collection.
     * @param PaginatorInterface $paginator : L'interface pour gerer la pagination.
     * @param Request $req : La requête contenant les paramètres de recherche.
     * @return Response La réponse contenant la page des résultats.
     */
    #[Route('/collection', name: 'app_collection')]
    public function index(CartRepository $cartRepo, CollectionsRepository $collectionRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $collectionList = $collectionRepo->getCollections();
        $collectionCount = $collectionRepo->countCollections();

        if ($collectionCount<8){
            $pagination = $paginator->paginate($collectionList,$request->query->getInt('page', 1), $collectionCount);
        }else{
            $pagination = $paginator->paginate($collectionList,$request->query->getInt('page', 1), 7);
        }
        
        return $this->render('collection/index.html.twig', [
            'pagination' => $pagination,
            'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
            'research'=>"",
        ]);
    }
}
