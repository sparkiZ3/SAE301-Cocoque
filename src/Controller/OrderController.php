<?php

namespace App\Controller;

use App\Repository\AdressRepository;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\StockRepository;
use App\Services\OrderNumberProvider;
use App\Form\AdressOrderFormType;
use App\Entity\Adress;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur en charge de la gestion des commandes, depuis la sélection des adresses jusqu'à la création de la commande finale.
 */
#[isGranted('ROLE_USER')]
class OrderController extends AbstractController
{
     /**
     * Affiche la page de commande et permet à l'utilisateur de saisir ses adresses de facturation et de livraison.
     * Cette action gère plusieurs étapes de commande, avec des formulaires pour l'adresse de facturation et l'adresse de livraison.
     *
     * @Route("/order", name="app_order")
     * @param Request $req : La requête.
     * @param EntityManagerInterface $entityManager : L'interface pour l'accès à la base de données via Doctrine.
     * @param AdressRepository $adressRepo : Le repository pour accéder aux adresses des utilisateurs.
     * @param UserRepository $userRepo : Le repository pour accéder aux infos de l'utilisateur.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param ProductRepository $productRepo : Le repository pour accéder aux infos sur les produits.
     *
     * @return Response : La réponse contenant la vue de la page de commande.
     */
    #[Route('/order', name: 'app_order')]
    public function index(Request $req,EntityManagerInterface $entityManager,AdressRepository $adressRepo, UserRepository $userRepo,CartRepository $cartRepo ,ProductRepository $productRepo): Response
    {
        $session = $req->getSession();
        $newAdressFacturation = new Adress();
        $newAdressLivraison = new Adress();
        $newAdressFacturation->setUserId($this->getUser()->getId());
        $newAdressLivraison->setUserId($this->getUser()->getId());
        $addrFormFacturation = $this->createForm(AdressOrderFormType::class ,$newAdressFacturation);
        $addrFormLivraison = $this->createForm(AdressOrderFormType::class ,$newAdressLivraison);

        $addrFormFacturation->handleRequest($req);
        $addrFormLivraison->handleRequest($req);

        if($session->get('pageStep') == null){
            $session->set('pageStep', 2);
        }

        if ($addrFormFacturation->isSubmitted() && $addrFormFacturation->isValid()) {
            $entityManager->persist($newAdressFacturation);
            $entityManager->flush();

            $session->set('pageStep', 3);
            $session->set('adrrFactuId', $newAdressFacturation->getId());
            return $this->redirectToRoute('app_order');
        }
        if ($addrFormLivraison->isSubmitted() && $addrFormLivraison->isValid()) {
            $entityManager->persist($newAdressLivraison);
            $entityManager->flush();

            $session->set('pageStep', 4);
            $session->set('adrrLivrId', $newAdressLivraison->getId());
            return $this->redirectToRoute('app_order');
        }

        $user= $this->getUser();
        $datas = $this->getDatas($adressRepo,$userRepo,$cartRepo,$productRepo);
        if($datas['totalCartPrice']==0){
            return $this->redirectToRoute('app_cart');
        }
        return $this->render('order/index.html.twig', [
            'user'=>$user,
            'cartElements'=>$datas['cartElements'],
            'totalCartPrice'=>$datas['totalCartPrice'],
            'deliveryPrice'=>$datas['deliveryPrice'],
            'adresses'=>$adressRepo->getAllAdressById($user->getId()),
            'cartCount' =>0,
            'deliveryPageStep'=>$session->get('pageStep'),
            'addrFormFacturation'=>$addrFormFacturation,
            'addrFormLivraison'=>$addrFormLivraison,
        ]);
    }
    /**
     * Gère le choix des adresses déja existante (facturation ou livraison) et met à jour les étapes du processus de commande.
     *
     * @Route("/order/createAdressOrNextStep/{type}", name="setEmailOrNextStep_order")
     * @param string $type : Type d'adresse (facturation ou livraison).
     * @param Request $req : La requête.
     * @param OrderRepository $orderRepo : Repository pour accéder aux commandes.
     * @param OrderNumberProvider $orderNumberProvider : Service pour générer les numéros de commande.
     * @param AdressRepository $adressRepo : Repository pour accéder aux adresses des utilisateurs.
     * @param UserRepository $userRepo : Repository pour accéder aux infos de l'utilisateur.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param ProductRepository $productRepo : Repository pour accéder aux infos sur les produits.
     *
     * @return Response : La réponse redirige l'utilisateur vers la page de commande.
     */
    #[Route('/order/createAdressOrNextStep/{type}', name: 'setEmailOrNextStep_order')]
    public function setEmailOrNextStep_order(string $type,Request $req,OrderRepository $orderRepo , OrderNumberProvider $orderNumberProvider,AdressRepository $adressRepo, UserRepository $userRepo,CartRepository $cartRepo ,ProductRepository $productRepo): Response
    {
        $user = $this->getUser();
        $session = $req->getSession();
        $selectedAdress = $req->request->get('selectedAdress');
        if($selectedAdress != "empty"){
            if($type == "facturation"){
                $session->set('pageStep', 3);
                $session->set('adrrFactuId', $selectedAdress);
            }else if($type == "livraison"){
                $session->set('pageStep', 4);
                $session->set('addrLivrId',$selectedAdress);
            }
        }
        return $this->redirectToRoute('app_order');
    }

    /**
     * Crée une commande à partir des informations fournies.
     * Gère également la gestion des stocks et la validation de la commande.
     *
     * @Route("/order/create", name="create_order")
     * @param Request $req : La requête.
     * @param OrderRepository $orderRepo : Repository pour accéder aux commandes.
     * @param ProductRepository $productRepo : Repository pour accéder aux infos sur les produits.
     * @param StockRepository $stockRepo : Repository pour accéder aux infos de stock.
     *
     * @return Response : La réponse redirige l'utilisateur vers la page du panier ou un message d'erreur.
     */
    #[Route('/order/create', name: 'create_order')]
    public function create(Request $req,OrderRepository $orderRepo,ProductRepository $productRepo, StockRepository $stockRepo): Response
    {
        try{
            $date= new \DateTime();
            $qte = $req->request->get('quantity');
            $session = $req->getSession();
            $adrrFactuId=$session->get('adrrFactuId');
            $adrrLivrId=$session->get('addrLivrId');

            $orderElements=$orderRepo->validateCartToOrder($this->getUser()->getId(),$adrrFactuId,$adrrLivrId);
            $session=$req->getSession();
            $session->invalidate();
            return $this->redirectToRoute('app_cart');
        }catch(\Exception $e) {
            $session=$req->getSession();
            $session->invalidate();
            $this->addFlash('error',"Erreur: Stock insuffisant, veuillez attendre un restockage ou commander moins de produit.");
            return $this->redirectToRoute('app_cart');
        }
        
    }
    /**
     * Récupère les informations nécessaires pour afficher le contenu du panier.
     *
     * @param AdressRepository $adressRepo : Repository pour accéder aux adresses des utilisateurs.
     * @param UserRepository $userRepo : Repository pour accéder aux infos de l'utilisateur.
     * @param CartRepository $cartRepo : Le repository permettant d'accéder aux infos du panier.
     * @param ProductRepository $productRepo : Repository pour accéder aux infos sur les produits.
     *
     * @return array : Un tableau contenant les éléments du panier, le prix total et le prix de livraison.
     */
    public function getDatas(AdressRepository $adressRepo, UserRepository $userRepo,CartRepository $cartRepo ,ProductRepository $productRepo):array{
        $user= $this->getUser();
        $cartElements = $cartRepo->getCartElementsById($user->getId());
        $totalCartPrice = 0;
        $allElements =[];
        foreach( $cartElements as $cartElement ){
            $el['product'] = $productRepo->getOneProductArrayById($cartElement['productId']);
            $el['quantity'] = $cartElement['quantity'];
            $el['id'] = $cartElement['id']; //l'id de la commande d'un produit et non d'un produit puisque un produit peut etre commandé plusieurs fois
            $totalCartPrice += $el['product']['unitPrice']*$el['quantity'];
            $allElements[] = $el;
        }
        if ($totalCartPrice>50){
            $deliveryPrice=0;
        }else{
            $deliveryPrice=$totalCartPrice*0.10;
        }
        return ['cartElements'=>$allElements,'totalCartPrice'=>$totalCartPrice, 'deliveryPrice'=>$deliveryPrice];
    }
    /**
     * Permet de naviguer entre les étapes de la commande (étape 2 à 4).
     * Mmet à jour l'étape de la commande dans la session (entre les étapes 2 et 4).
     *
     * @Route("/order/goToStep/{step}", name="order_gotoStep")
     * @param int $step : L'étape vers laquelle l'utilisateur veut aller.
     * @param Request $req : La requête.
     *
     * @return Response : La réponse redirigeant l'utilisateur vers la page de commande.
     */
    #[Route('/order/goToStep/{step}', name: 'order_gotoStep')]
    public function goToStep(int $step,Request $req): Response
    {
        $session = $req->getSession();
        if( $step >=2 && $step<=4 ){
            $session->set('pageStep',$step);
        }

        return $this->redirectToRoute('app_order');
    }
}
