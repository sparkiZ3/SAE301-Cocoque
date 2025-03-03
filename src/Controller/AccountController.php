<?php

namespace App\Controller;

use App\Repository\AdressRepository;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\UserAccountFormType;
use App\Form\AdressAccountFormType;
use Doctrine\ORM\EntityManagerInterface;

#[isGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    /**
     * Affiche les informations du compte de l'utilisateur connecté
     *
     * Récupère les informations sur l'utilisateur tel que :
     *  - ses informations personnelles enregistrés et son historique de commande
     * 
     * Une redirection vers la page de connexion est effectué quand l'utilisateur ne
     * s'est pas authentifié.
     *
     * @param AddressRepository $addressRepo Le repository pour accéder aux adresses.
     * @param CartRepository $cartRepo Le repository pour accéder aux informations du panier.
     * @param OrderRepository $orderRepo Le repository pour recupérer les commandes.
     * @param ProductRepository $productRepo Le repository pour accéder aux produits présents dans le catalogue.
     * 
     * @return Response La réponse contenant le produit spécifié.
     */
    #[Route('/account', name: 'app_account')]
    public function index(AdressRepository $adressRepo,CartRepository $cartRepo, OrderRepository $orderRepo, ProductRepository $productRepo): Response
    {
        $orderElements = $orderRepo->getOrderById($this->getUser()->getId());
        $allOrder =[];
        $groupedOrders = [];

        foreach( $orderElements as $orderElement ){  
            $orderNumber = $orderElement['orderNumber'];
            $groupedOrders[$orderNumber] []= [
                'product' => $productRepo->getOneProductArrayById($orderElement['productId']),
                'quantity' => $orderElement['quantity'],
                'id' => $orderElement['id'],
            ];
        
        }

        $allOrder = [];
        foreach ($groupedOrders as $orderNumber => $orders) {
                $allOrder[] = [
                    'orderNumber' => $orderNumber,
                    'products' => $orders,
                ];
        }
            

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
            'adress'=> $adressRepo->getFirstAdressById($this->getUser()->getId()),
            'cartCount'=>$cartRepo-> cartsizeByUser($this->getUser()),
            'orderElements'=> $allOrder,
        ]);
    }


    /**
     * Permet à l'utilisateur de modifier ses informations de compte et ajouter une adresse.
     *
     * Deux formulaires sont utilisés : un pour les informations de l'utilisateur 
     * et un autre pour l'adresse. Lorsque les formulaires sont soumis et validés, 
     * les informations sont enregistrées dans la base de données.
     *
     *
     * @param Request $request La requête HTTP contenant les informations soumises par l'utilisateur.
     * @param AdressRepository $adressRepo Le repository utilisé pour récupérer et gérer les adresses de l'utilisateur.
     * @param EntityManagerInterface $entityManager L'interface de gestion des entités pour persister les données en base de données.
     *
     * @return Response La réponse contenant le rendu du formulaire ou la redirection vers la page de compte.
     */
    #[Route('/account/edit', name: 'app_account_edit')]
    public function edit(Request $request,AdressRepository $adressRepo,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userform = $this->createForm(UserAccountFormType::class, $user);
        $adrr = $adressRepo->getFirstAdressById($user->getId());
        $addrform = $this->createForm(AdressAccountFormType::class, $adrr);

        $userform->handleRequest($request);
        $addrform->handleRequest($request);

        if ($userform->isSubmitted() && $userform->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_account');
        }
        if ($addrform->isSubmitted() && $addrform->isValid()) {
            $entityManager->persist($adrr);
            $entityManager->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/indexEdit.html.twig', [
            'user' => $this->getUser(),
            'userForm' => $userform,
            'adressDefined' => $adrr !=null,
            'adressForm'=> $addrform,
            'cartCount'=>0,
        ]);
    }
}
