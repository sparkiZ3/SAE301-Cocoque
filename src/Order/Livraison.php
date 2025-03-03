<?php

namespace App\Order;


class Livraison
{
    public array $newAdressInfo;
    public string $selectedAdress;
    public function __construct(array $adressInfos)
    {
        $this->newAdressInfo = $adressInfos["newAdressData"];
        $this->selectedAdress = $adressInfos["selectedAdress"];
    }

    public function getNewAdressData(){
        return $this->newAdressInfo;
    }
    public function getSelectedAdress(){
        return $this->selectedAdress;

    }

    public function giveNextPageStep(){
        return 4;
    }
}