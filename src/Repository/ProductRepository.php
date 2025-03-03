<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repository en charge de la gestion des entités Produits.
 * Fournit des méthodes pour manipuler les produits dans la base de données.
 *
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository des modèles de téléphone.
     *
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     * @param EntityManagerInterface $em : Gestionnaire des entités pour effectuer les persistances et flush.
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Product::class);
        $this->em = $em;
    }

    /**
     * Récupère un produit sous forme de tableau par id.
     *
     * @param int $id : L'identifiant du produit à récupérer.
     * @return array : Le produit récupéré sous forme de tableau.
     */
    public function getOneProductArrayById(int $id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult()[0];
    }
    /**
     * Récupère les produits correspondant à un ensemble de labels.
     * 
     * @param array $labels : Un tableau de labels à rechercher dans les produits.
     * @return array : Un tableau de produits correspondant aux labels recherchés.
     */
    public function getProductsByLabels(array $labels) {
        $builder = $this->createQueryBuilder('p');
        
        foreach ($labels as $index => $label) {
            if ($index === 0) {
                $builder->where('p.labels LIKE :label'.$index);
            } else {
                // Autres labels : on ajoute des AND WHERE
                $builder->andWhere('p.labels LIKE :label'.$index);
            }
            $builder->setParameter('label'.$index, '%'.$label.'%');
        }
        return $builder->getQuery()->getResult();
    }

    /**
     * Récupère le nombre total de produits dans la base de données.
     *
     *
     * @return int : Le nombre total de produits.
     */
    public function getTotalProducts():int{
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trie les produits par prix dans une plage définie et selon l'ordre (ascendant/descendant).
     *
     * @param string $type : Le type de tri ("ASC" ou "DESC").
     * @param int $min : Le prix minimal des produits.
     * @param int $max : Le prix maximal des produits.
     * @return array : Un tableau de produits triés selon les critères définis.
     */
    public function sortByPrice(string $type, int $min , int $max): array{
        return $this->createQueryBuilder('u')
            ->where('u.unitPrice > :min')
            ->andWhere('u.unitPrice < :max')
            ->orderBy('u.unitPrice', $type)
            ->setParameter('min',$min)
            ->setParameter('max',$max)
            ->getQuery()
            ->getResult();
    }

    /**
     * Crée un nouveau produit dans la base de données avec une quantité.
     *
     * Cette méthode utilise une procédure stockée pour créer un produit dans la base de 
     * données
     *
     * @param Product $product : L'objet représentant le produit à créer.
     * @param int $quantity : La quantité du produit à ajouter.
     * @return void
     */
    public function createProduct( Product $product, int $quantity ) {
        $connection = $this->em->getConnection();
        $sql = 'CALL CREER_PRODUIT(:product_name, :description, :unit_price, :image_url, :color, :model_name, :labels, :collection_id, :quantity)';
        $stmt = $connection->prepare($sql);

        // attributs
        $name = $product->getProductName();
        $description = $product->getDescription();
        $unit_price = $product->getUnitPrice();
        $image_url = $product->getImageURL();
        $color = $product->getColor();
        $model_name = $product->getModelName();
        $labels = $product->getLabels();
        $collection = $product->getCollectionId();
        
        // Lier les paramètres d'entrée
        $stmt->bindParam(':product_name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':unit_price', $unit_price);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':model_name', $model_name);
        $stmt->bindParam(':labels', $labels);
        $stmt->bindParam(':collection_id', $collection);
        $stmt->bindParam(':quantity', $quantity);

        // Exécuter la requête
        $stmt->execute();
        return ;
    }
     /**
     * Cette méthode permet de récupérer tous les produits appartenant à une collection donnée,
     *  à l'exception du produit en question.
     *
     * @param int $collecId : L'id de la collection dont on récupére les produits.
     * @param int $prodId : L'id du produit à exclure des résultats.
     * @return array : Un tableau de produits de la même collection (sauf le produit en question).
     */
    public function getProductFromCollection($collecId,$prodId):array{
        return $this->createQueryBuilder('p')
            ->where('p.collectionId = :collecId')
            ->andWhere('p.id != :prodId')
            ->setParameter('collecId', $collecId)
            ->setParameter('prodId', $prodId)
            ->getQuery()
            ->getResult();
    }


}