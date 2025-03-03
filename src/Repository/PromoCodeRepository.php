<?php

namespace App\Repository;

use App\Entity\PromoCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromoCode>
 */
class PromoCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoCode::class);
    }

    /**
     * Récupère l'ID du code promo en fonction de son nom.
     *
     * Cette méthode interroge la base de données pour obtenir l'ID d'un code promo en 
     * fonction du nom du code promo fourni. Si le code promo n'existe pas, la méthode 
     * retourne 0. Sinon, elle retourne l'ID du code promo.
     *
     * @param string $codeName Le nom du code promo à rechercher.
     *
     * @return int L'ID du code promo, ou 0 si le code promo n'existe pas.
     */
    public function getPromoCodeIdByName(string $codeName): ?int{
        $res = $this->createQueryBuilder('p')
        ->select('p.id')
        ->where('p.codeName = :code')
        ->setParameter('code',$codeName)
        ->getQuery()
        ->getOneOrNullResult();
        
        return $res ? $res['id'] : null;
    }

    /**
     * Récupère la remise d'un code promo par son ID.
     *
     * Cette méthode interroge la base de données pour obtenir la remise (discount) associée 
     * à un code promo en fonction de l'ID fourni. Si le code promo n'existe pas ou s'il n'a 
     * pas de remise, la méthode retourne 0. Sinon, elle retourne la remise associée à ce code promo.
     *
     * @param int $id L'ID du code promo dont on souhaite obtenir la remise.
     *
     * @return int La remise du code promo, ou 0 si le code promo n'existe pas ou n'a pas de remise.
     */
    public function getPromoCodeDiscountById(int $id): ?int{
        $res = $this->createQueryBuilder('p')
        ->select('p.discount')
        ->where('p.id = :id')
        ->setParameter('id',$id)
        ->getQuery()
        ->getOneOrNullResult();
        return $res ? $res['discount'] : null;
    }
}
