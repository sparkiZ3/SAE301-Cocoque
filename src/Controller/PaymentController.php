<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Payment\Payment;
use App\Payment\PaymentProxy;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use App\Repository\PromoCodeRepository;

// Controller en charge du payment, gère l'interaction entre le dossier payment et la vue payment.
class PaymentController extends AbstractController
{
    /**
     * Présente la page de payment avec les informations souhaitées.
     *
     * Cette action récupère une session établie par les fonctions précédentes et 
     * affiche les paramètres de cette session sur la page de payment.
     * Les paramètres sont(totalAmount: Le prix total, cartCount: Le nombre d'élément dans le panier
     * , paymentMethod: La méthode de payment choisi, delivery: Le type de livraison choisi, 
     * promocode: Le code promo, s'il existe).
     *
     * @param Request $req :L'entiereté de la requête envoyé par la page.
     * @return Response La réponse contenant la page à chargé avec les paramètres souhaités.
     */
    #[Route('/payment', name: 'app_payment')]
    public function index(Request $req): Response
    {
        $session = $req->getSession();
        return $this->render('payment/index.html.twig', [
            'totalAmount' => $session->get('totalAmount'),
            'cartCount'=>0,
            'paymentMethod' =>$session->get('paymentMethod'),
            'delivery' => $session->get('delivery'),
            'promoCode'=> $session->get('promoCode'),
        ]);
    }
    /**
     * Gère l'ajout d'un code promo
     *
     * Cette action récupère un Stock de code Promo et une requête avec les infos fournis par les fonctions et la vue.
     *  Si le promoCode existe, alors on réduit le prix,sinon le promoCode ne modifie rien.  
     * @param Request $req :L'entiereté de la requête envoyé par la page.
     * @param PromoCodeRepository $promoRepo :L'accès aux codes promos, pour appeler les méthodes.
     * @return * redirige sur la fonction index en charge d'afficher la page.
     */
    #[Route('/payment/promo',name: 'payment_promo')]
    public function promoCode(Request $req,PromoCodeRepository $promoRepo){
        $session = $req->getSession();
        $promoCode=$req->request->get('codePromo');
        if ($session->get('promoCode')!=null){
            return $this->redirectToRoute('app_payment');
        }
        if ($promoRepo->getPromoCodeIdByName($promoCode)!=null){
            $promoCodeId=$promoRepo->getPromoCodeIdByName($promoCode);
            if ($promoCodeId!=null){
                $discount=$promoRepo->getPromoCodeDiscountById($promoCodeId);
            }
                
        }else{
            $promoCodeId=null;
            $discount=0;
        }
        $totalAmount=$session->get('totalAmount');
        $totalAmount=intval($totalAmount)*((100-$discount)/100);
        $session->set('totalAmount', $totalAmount);
        if ($promoCodeId==null){
            return $this->redirectToRoute('app_payment');
        }
        $session->set('promoCode', $promoCode);
        return $this->redirectToRoute('app_payment');
    }

    /**
     * Traite le paiement en fonction des informations fournies par l'utilisateur.
     *
     * Cette action configure la méthode de paiement et le mode de livraison,
     * calcule le montant total en incluant les frais associés, et redirige
     * l'utilisateur vers la page de paiement ou le panier en cas d'erreur.
     *
     * @param Request $req La requête contenant les données du paiement.
     * @param PaymentProxy $proxy Le design pattern proxy utilisé pour traiter le paiement.
     * 
     * @return Response Redirige vers la page de paiement en cas de succès ou le panier en cas d'échec.
    */
    #[Route('/payment/process',name: 'payment_process')]
    public function processPayment(Request $req, PaymentProxy $proxy): Response{

        $session = $req->getSession();
        $session->set('pageStep', null);

        $paymentMethod= $req->request->get('paymentMethod');
        $delivery= $req->request->get('delivery');
        $amount= $session->get('totalAmount');
        if ($delivery!="fast"){
            $delivery="standard";
        }
        $session = $req->getSession();
        $session->set('delivery', $delivery);
        $session->set('paymentMethod', $paymentMethod);
        try{
            $payment = Payment::factoryPayment($paymentMethod,$delivery);
            $proxy->setPayment($payment);
            $totalAmount = $proxy->totalAmount(floatval($amount));
            $session->set('totalAmount', $totalAmount);
            return $this->redirectToRoute('app_payment');
        }catch(\Exception $e){
            $this->addFlash('error',$e->getMessage());
            return $this->redirectToRoute('app_cart');
        }
    }

}