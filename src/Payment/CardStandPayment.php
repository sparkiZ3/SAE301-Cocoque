<?php
namespace App\Payment;
//Sous-classe de Payment.
class CardStandPayment extends Payment{
    protected string $paymentMethod;
    protected string $delivery;
    public function __construct($paymentMethod, $delivery){
        $this->paymentMethod = $paymentMethod;
        $this->delivery = $delivery;
    }
    public function totalAmount(float $amount): float{
        return $amount+1;
    }
}