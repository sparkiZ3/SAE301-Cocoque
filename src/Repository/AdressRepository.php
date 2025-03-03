<?php

namespace App\Repository;

use App\Entity\Adress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Repository en charge des entités Adress.
 * Fournit des méthodes pour récupérer et manipuler les adresses associées à un utilisateur.
 *
 * @extends ServiceEntityRepository<Adress>
 */
class AdressRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository d'adresses.
     * 
     * @param ManagerRegistry $registry : Fournit accès à la gestion des entités Doctrine.
     * @param HttpClientInterface $client : Client HTTP pour effectuer des appels à des API externes.
     */
    public function __construct(ManagerRegistry $registry,private HttpClientInterface $client,)
    {
        parent::__construct($registry, Adress::class);
        
    }
    /**
     * Récupère la première adresse associée à un utilisateur via son ID.
     *
     * @param int $id : L'identifiant de l'utilisateur.
     * @return Adress|null : L'entité Adress correspondante ou null si aucune adresse n'est trouvée.
     */
    public function getFirstAdressById(int $id): ?Adress{
        return $this->findOneBy(["userId"=> $id]);
    }

    /**
     * Récupère toutes les adresses associées à un utilisateur via son ID.
     *
     * @param int $id : L'identifiant de l'utilisateur.
     * @return array|null : Un tableau des entités Adress ou null si aucune adresse n'est trouvée.
     */
    public function getAllAdressById(int $id): ?array{
        return $this->findBy(["userId"=> $id]);
    }

    /**
     * Récupère les noms des adresses associées à un utilisateur via son ID.
     *
     * @param int $id : L'identifiant de l'utilisateur.
     * @return array : Un tableau associatif contenant les noms des rues et leurs IDs. Retourne "--" si aucune adresse n'est trouvée.
     */
    public function getAllAdressNameByUserId(int $id): ?array{
        $adresses = $this->findBy(["userId"=> $id]);
        $returnedValues["empty"]="--";
        foreach ($adresses as $adress){
            $returnedValues[$adress->getStreetAdress()] = $adress->getId();
        }
        return $returnedValues;
    }


    /**
     * Récupère les villes correspondant à un code postal via une API externe.
     *
     * Cette méthode utilise un client HTTP pour effectuer une requête GET vers l'API d'IGN.
     *
     * @param string $zipcode : Le code postal pour lequel récupérer les villes.
     * @return array : Un tableau des villes correspondantes.
     */
    public function getCitysPerZipCode(string $zipcode): array
    {
        $response = $this->client->request(
            'GET',
            'https://apicarto.ign.fr/api/codes-postaux/communes/'.$zipcode
        );
        $content = $response->toArray();
        
        return $content;
    }

}
