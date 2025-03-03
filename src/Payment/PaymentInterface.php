<?php
namespace App\Payment;

interface PaymentInterface
{
    /**
     * @return array : Renvoi une array de paymentMethod et de delivery.
     */
    public function paymentDetails(): array;


    /**
     * @param float amount : Le total avant modification. 
     * @return float : Retourne le total du prix après modification.
     */
    public function totalAmount(float $amount): float;
}

