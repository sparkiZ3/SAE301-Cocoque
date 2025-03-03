<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Collections;
use App\Entity\PromoCode;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CollectionsRepository;
use App\Repository\AdressRepository;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Form\UserAccountFormType;
use App\Form\AdressAccountFormType;
use App\Repository\PromoCodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\CreateNewProductFormType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;


#[isGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{

     /**
     * Route principale de la partie admin.
     *
     * Cette route permet de gérer toutes la page /admin. Elle gere la recherche 
     * et la création d'entités (collections et produits)
     * 
     * 
     * @param Request $req Sert a recuperer les éléments przsent dans la requete
     * @param OrderRepository $orderRepo Le repository pour communiquer avec la table des commandes.
     * @param UserRepository $userRepo Le repository pour communiquer avec la table des utilisateurs.
     * @param ProductRepository $productRepo Le repository pour communiquer avec la table des Produits.
     * @param CollectionsRepository $collecrepo Le repository pour communiquer avec la table des Collections.
     * @param SluggerInterface $slugger Sert a générer des slug pour la création des images des produits afin d'avoir des noms unique
     * @param #[Autowire('%kernel.project_dir%/public/imgs/products')] string $brochuresDirectory Chemin du répertoire où les images des produits sont stockées.
     * @param EntityManagerInterface $entityManager L'Entity Manager sert a gérer les entités dans la BDD.
     * 
     * 
     * @return Response qui render la page twig
     */

    #[Route('/admin', name: 'app_dashboard')]
    public function index(
        Request $req,
        OrderRepository $orderRepo,
        UserRepository $userRepo ,
        ProductRepository $productRepo,
        CollectionsRepository $collecrepo,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/imgs/products')] string $brochuresDirectory,
        EntityManagerInterface $entityManager
    ): Response
    {
        //recuperation des informations de la requête
        $searchUser = $req->request->get('searchUser');
        $searchOrder = $req->request->get('searchOrder');
        $createCollection = $req->request->get('createCollection');
        $searchCollec = $req->request->get('searchCollec');

        $newProduct = new Product();
        $formCreateProduct = $this->createForm(CreateNewProductFormType::class, $newProduct);
        $formCreateProduct->handleRequest($req);
        
        //gestion des variables de recherches pour mettre les bonnes classes en fonction de la bonne recherche
        $users =[]; //listes des utilisateurs qui vont etre affichés
        $orders =[];//listes des commandes qui vont etre affichés
        $collections =[];//listes des collections qui vont etre affichés
        if($searchUser != null){ //dans le cas ou l'utilisateur fait une recherche d'un user
            $users = $userRepo->searchUserBy($searchUser); 
        }else{
            $users = $userRepo->get20Users();
        }
        
        //!\\ Cette condition n'est pas fonctionelle car elle n'est pas implementé
        if($searchOrder != null){ //dans le cas ou l'utilisateur fait une recherche d'un order 
            $orderNumbers = $orderRepo->getOrderNumbers(20);
        }else{
            $orderNumbers = $orderRepo->getOrderNumbers(20);
        }

        if($searchCollec != null){ //dans le cas ou l'utilisateur fait une recherche d'une collection
            $collections = $collecrepo->searchCollecBy($searchCollec); 
        }else{
            $collections = $collecrepo->getCollections();
        }


        //---| gestion des formulaires de création |---
        //gestion du formulaire de creation de collections
        if($createCollection != null && $createCollection != ""){
            $newCollection = new Collections();
            $newCollection->setCollectionName($createCollection);
            $entityManager->clear();
            $entityManager->persist($newCollection);
            $entityManager->flush();
        }
        //gestion du formulaire de creation de produits -> https://symfony.com/doc/current/controller/upload_file.html
        if ($formCreateProduct->isSubmitted() && $formCreateProduct->isValid()) {
            $productImg = $formCreateProduct->get('productImg')->getData(); //on recupere les informations sur le fichier déposé
            $originalFilename = pathinfo($productImg->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename); // on genere un nouveau nom de fichier unique avec un slug
            $newFilename = $safeFilename.'-'.uniqid().'.'.$productImg->guessExtension(); //on genere le nom avec le bon format
            $newProduct->setImageURL($newFilename); // on définit dans la bdd le nom du fichier
            $quantity = $formCreateProduct->get('quantity')->getData(); //on recupere la quantité du stock initial initialisé dans le formulaire
            try {
                $productImg->move($brochuresDirectory, $newFilename); //on déplace le fichier au bon endroit
            } catch (FileException $e) {
                dd($e);
            }
            $productRepo->createProduct($newProduct, $quantity); //on créé le produit
        }


        $overviewValues=$this->getOverviewValues($orderRepo,$userRepo,$productRepo); //recuperation du total de commandes, de user et de produits
        
        return $this->render('dashboard/index.html.twig', [
            'users'=>$users,
            'formCreateProduct'=>$formCreateProduct,
            'orders'=>$orders,
            'cartCount'=>0,
            'overviewValues'=>$overviewValues,
            'collections'=>$collections,
            'orderNumbers'=>$orderNumbers,
        ]);
    }


    /**
     * Permet de recuperer des informations générales.
     *
     * Cette fonction permet de recuperer le nombre de commandes passée, le nombre d'utilisateurs créés
     * et le nombre de produits présent dans la base de données
     * 
     * Ces valeurs seront affichées dans la partie 'overview' de la page
     *
     * @param OrderRepository $orderRepo Le repository pour communiquer avec la table des commandes.
     * @param UserRepository $userRepo Le repository pour communiquer avec la table des utilisateurs.
     * @param ProductRepository $productRepo Le repository pour communiquer avec la table des Produits.
     * 
     * @return Array $datas qui contient le total des éléments souhaité
     */
    public function getOverviewValues(OrderRepository $orderRepo, UserRepository $userRepo, ProductRepository $productRepo):array{
        $datas = [];
        $datas[] = $orderRepo->getTotalElements();
        $datas[]= $userRepo->getTotalUsers();
        $datas[]= $productRepo->getTotalProducts();

        return $datas;

    }

    /**
     * Permet de supprimer une collection.
     *
     * Cette route permet de supprimer une collection en fonction de son id
     * qui est passé en parametre
     * 
     *
     * @param int $id L'identifiant unique de la collection qui va etre supprimé.
     * @param CollectionsRepository $collecrepo Le repository pour communiquer avec la table des collections.
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirige l'utilisateur vers la route 'app_dashboard'
     *         qui affiche le tableau de bord administrateur.
     */
    #[Route('/admin/collec/delete/{id}', name: 'app_deletecollec')]
    public function deleteCollec(int $id,CollectionsRepository $collecrepo,){
        $collecrepo->deleteCollec($id);
        return $this->redirectToRoute('app_dashboard');
    }


    /**
     * Permet de supprimer un utilisateur.
     *
     * Cette route permet de supprimer un utilisateur en fonction de son id
     * qui est passé en parametre
     * 
     * Cependant, il est impossible de supprimer un utilisateur qui est admin
     *
     * @param int $id L'identifiant unique de l'utilisateur qui va (si il n'est pas admin) etre supprimé.
     * @param UserRepository $userRepo Le repository pour communiquer avec la table des utilisateurs.
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirige l'utilisateur vers la route 'app_dashboard'
     *         qui affiche le tableau de bord administrateur.
     */
    #[Route('/admin/user/delete/{id}', name: 'app_deleteUser')]
    public function deleteUser(int $id,UserRepository $userRepo){
        $userRepo->deleteUser($id);
        return $this->redirectToRoute('app_dashboard');
    }
    
    /**
     * Permet de créer un code de promotion.
     *
     * Cette route permet de récupérer un nom de code promo et un pourcentage de rabais
     * via la requête POST, puis, en utilisant l'EntityManager, un nouveau code promo
     * est créé et enregistré dans la base de données.
     * 
     * Après la création du code promo, l'utilisateur est redirigé vers le tableau de bord administrateur.
     *
     * @param Request $req La requête HTTP contenant les données du code promo et du pourcentage.
     * @param PromoCodeRepository $promocodeRepo Le repository pour communiquer avec la table des codes promo.
     * @param EntityManagerInterface $entityManager L'Entity Manager pour gérer les entités.
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Redirige l'utilisateur vers la route 'app_dashboard'
     *         qui affiche le tableau de bord administrateur.
     */
    #[Route('/admin/promocode/create', name: 'app_promoCreate')]
    public function createpromocode(Request $req,PromoCodeRepository $promocodeRepo,EntityManagerInterface $entityManager){
        $promocode = $req->request->get('promocode');
        $percentage = $req->request->get('percentage');

        if($promocode != null && $percentage != null){
            $newPromoCode = new PromoCode();
            $newPromoCode->setCodeName($promocode);
            $newPromoCode->setDiscount($percentage);
            $entityManager->clear();
            $entityManager->persist($newPromoCode);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_dashboard');
    }
    #[Route('/admin/user/edit/{id}', name: 'app_editUser')]
    public function modifUser(int $id,Request $request,EntityManagerInterface $entityManager ,UserRepository $userRepo,AdressRepository $adressRepo,CartRepository $cartRepo){

        $user=$userRepo->findOneBy(['id'=> $id]);
        $userform = $this->createForm(UserAccountFormType::class, $user);
        $adrr = $adressRepo->getFirstAdressById($user->getId());
        $addrform = $this->createForm(AdressAccountFormType::class, $adrr);

        $userform->handleRequest($request);
        $addrform->handleRequest($request);

        if ($userform->isSubmitted() && $userform->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        if ($addrform->isSubmitted() && $addrform->isValid()) {
            $entityManager->persist($adrr);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('account/indexEditAdmin.html.twig', [
            'user' => $user,
            'adress'=> $adressRepo->getFirstAdressById($user->getId()),
            'cartCount'=>$cartRepo-> cartsizeByUser($user),
            'userForm' => $userform,
            'adressDefined' => $adrr !=null,
            'adressForm'=> $addrform,
            'cartCount'=>0,
        ]);
    }
}
