<?php

namespace App\Order;
use Symfony\Component\HttpFoundation\RequestStack;

class Facturation
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
    private function checkNewAdressInfos(array $adressInfos){//check les valeurs qui doivent pas etre null
        $toCheck=["country","adress","zipcode","city"];
        foreach ($toCheck as $el){
            if($adressInfos[$el] == ""){
                return false;
            }
        }
        return true;
    }
    public function getCreationStep(){
        $returnValues=[];
        if($this->getSelectedAdress()!="empty"){
            $returnValues["command"]="create";
            $returnValues["payload"]=$this->getNewAdressData();
        }else if($this->checkNewAdressInfos($this->getNewAdressData()) ){
            $returnValues["command"]="exist";
            $returnValues["payload"]=$this->getSelectedAdress();
        }
        return $returnValues;
    }

    public function giveNextPageStep(){
        return 3;
    }
}
