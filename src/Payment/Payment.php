<?php
namespace App\Payment;
use App\Payment\PaymentInterface;

/**
 * Classe abstraite Payment qui implémente PaymentInterface.
 *
 * Cette classe sert de base pour définir des méthodes de paiement. Elle contient les informations de base
 * telles que la méthode de paiement choisie et le type de livraison sélectionné. Elle permet également de créer
 * des instances spécifiques de paiement via la méthode statique `factoryPayment`.
 * 
 * Les classes dérivées doivent implémenter la méthode `totalAmount` pour calculer le montant total d'un paiement.
 */

abstract class Payment implements PaymentInterface{
    /**
     * La méthode de paiement choisie (par exemple, carte, PayPal).
     *
     * @var string
     */
    protected string $paymentMethod;

    /**
     * Le type de livraison choisi (par exemple, rapide, standard).
     *
     * @var string
     */
    protected string $delivery;

    /**
     * Constructeur pour initialiser la méthode de paiement et le type de livraison.
     *
     * Cette méthode initialise les propriétés de la classe avec les valeurs fournies pour la méthode de paiement
     * et le type de livraison.
     *
     * @param string $paymentMethod La méthode de paiement (par exemple, "card", "paypal").
     * @param string $delivery Le type de livraison (par exemple, "fast", "standard").
     */
    public function __constuct(string $paymentMethod, string $delivery){
        $this->paymentMethod = $paymentMethod;
        $this->delivery = $delivery;
    }


    /**
     * Méthode de création d'une instance de paiement spécifique en fonction de la méthode de paiement et de la livraison.
     *
     * Cette méthode statique permet de créer et retourner une instance spécifique de paiement (par exemple, 
     * `CardFastPayment`, `PaypalFastPayment`) en fonction de la méthode de paiement et du type de livraison.
     * Si les paramètres ne correspondent à aucune combinaison valide, une exception est levée.
     *
     * @param string $paymentMethod La méthode de paiement (par exemple, "card", "paypal").
     * @param string $delivery Le type de livraison (par exemple, "fast", "standard").
     * @return PaymentInterface L'instance de paiement spécifique créée.
     * @throws \Exception Si la méthode de paiement ou le type de livraison est invalide.
     */
    public static function factoryPayment(string $paymentMethod, string $delivery): PaymentInterface{
        if ($paymentMethod == "card" && $delivery=="fast"){
            return new CardFastPayment($paymentMethod, $delivery);
        }
        if ($paymentMethod == "card" && $delivery=="standard"){
            return new CardStandPayment($paymentMethod, $delivery);
        }
        if ($paymentMethod == "paypal" && $delivery=="fast"){
            return new PaypalFastPayment($paymentMethod, $delivery);
        }
        if ($paymentMethod == "paypal" && $delivery=="standard"){
            return new PaypalStandPayment($paymentMethod, $delivery);
        }
        throw new \Exception("The payment method or delivery is invalid");
    }
    abstract public function totalAmount(float $amount): float;

    public function paymentDetails(): array{
        return [
            'paymentMethod'=>$this->paymentMethod,
            'delivery'=> $this->delivery];
    }
}