<?php
namespace App\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Classe PaymentProxy qui sert de proxy pour l'exécution des paiements.
 * 
 * Cette classe implémente l'interface PaymentInterface et agit comme un intermédiaire
 * entre l'application et la logique de paiement réelle. Elle permet de gérer la méthode de paiement
 * choisie et de vérifier les droits d'accès avant de traiter le paiement.
 */
class PaymentProxy extends AbstractController implements PaymentInterface{
    /**
     * Instance de PaymentInterface représentant la méthode de paiement réelle.
     *
     * @var PaymentInterface|null
     */
    private ?PaymentInterface $payment = null;

    /**
     * Définit la méthode de paiement à utiliser.
     *
     * Cette méthode permet de définir une classe paiement (par exemple, carte de crédit avec livraison rapide, PayPal, etc.)
     * Cette classe sera utilisée pour le traitement du paiement, les méthode seront appelés sur cette classe payment.
     *
     * @param PaymentInterface $payment L'objet représentant la méthode de paiement à définir.
     */
    public function setPayment(PaymentInterface $payment){
            $this->payment=$payment;
    }
    /**
     * Calcule et renvoie le montant total à payer.
     *
     * Cette méthode permet de récupérer le montant total du paiement
     * à l'aide de la méthode de l'attribut payment définie.
     * Avant de retourner le montant total, la méthode vérifie également que la session de l'utilisateur est valide.
     *
     * @param float $amount Le montant de base du paiement à ajuster.
     * @return float Le montant total à payer, après ajustement.
     * @throws \RunTimeException Si le paiement n'est pas définie.
     */
    public function totalAmount(float $amount): float{
        $this->hasAccess();
        if($this->payment==null){
            throw new \RunTimeException("Payement not set");
        }
        return $this->payment->totalAmount($amount);
    }
    /**
     * Récupère les détails du paiement.
     *
     * Cette méthode retourne un tableau avec les détails relatifs à la méthode de l'attribut payment sélectionnée,
     * tels que  le type de paiement, la livraison, etc.
     *
     * @return array Un tableau contenant les détails du paiement.
     * @throws \RunTimeException Si la méthode de paiement n'est pas définie.
     */
    public function paymentDetails(): array{
        $this->hasAccess();
        if($this->payment==null){
            throw new \RunTimeException("Payement not set");
        }
        return $this->payment->paymentDetails();

    }
    /**
     * Vérifie si l'utilisateur a accès à la fonctionnalité de paiement.
     *
     * Cette méthode vérifie si l'utilisateur a le rôle "ROLE_VERIFIED" et si son compte est validé.
     * Si l'utilisateur n'a pas accès, une exception est lancée.
     *
     * @throws \Exception Si l'utilisateur n'a pas accès.
     */
    private function hasAccess():void{
        if (in_array("ROLE_VERIFIED",$this->getUser()->getRoles())){
            return;
        }else{
            throw new \Exception('Accès refusé: Veuillez vérifier votre mail avant de payer');
        }
    }
      

}
