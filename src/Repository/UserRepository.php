<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository en charge de la gestion des entités Utilisateurs.
 * Fournit des méthodes pour manipuler les utilisateurs dans la base de données.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructeur du repository des utilisateurs.
     * 
     * @param ManagerRegistry $registry : Gestionnaire des entités Doctrine.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */

    /**
     * Met à jour en hashant le mot de passe de l'utilisateur automatiquement.
     * 
     * Sécurise les mots de passe lors d'un changement de mot de passe.
     *
     * @param PasswordAuthenticatedUserInterface $user : L'utilisateur dont le mot de passe doit être mis à jour.
     * @param string $newHashedPassword : Le nouveau mot de passe haché.
     * 
     * @throws UnsupportedUserException Si l'utilisateur n'est pas une instance de `User`.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    /**
     * Récupère un nombre d'utilisateurs par défaut 20.
     *
     * @param int $maxResult : Le nombre maximum d'utilisateurs à récupérer.
     * @return User[] : Un tableau d'objets `User`.
     */
    public function get20Users(int $maxResult=20){
        return $this->createQueryBuilder('u')
        ->setMaxResults($maxResult)
        ->getQuery()
        ->getResult();
    }
    /**
     * Recherche des utilisateurs en fonction du prenom,nom ou pseudo.
     *
     * @param string $search : correspond à une partie du prénom, du nom ou du nom d'utilisateur.
     * @return User[] : Un tableau d'objets `User` correspondant aux critères de recherche.
     */
    public function searchUserBy($search){
        return $builder = $this->createQueryBuilder('u')
        ->where('u.firstName LIKE :search')
        ->orWhere('u.lastName LIKE :search')
        ->orWhere('u.username LIKE :search')
        ->setParameter('search', '%'.$search.'%')
        ->getQuery()
        ->getResult();
    }
    /**
     * Récupère un utilisateur en fonction de son email ou son pseudo.
     *
     * @param string $userNameOrEmail : L'email ou le nom d'utilisateur de l'utilisateur à rechercher.
     * @return User|null : Un `User` si un utilisateur correspond, sinon `null`.
     */
    public function findUserByEmailOrUsername(string $userNameOrEmail):?User{
        return $this->createQueryBuilder('u')
        ->where('u.email = :identifier')
        ->orWhere('u.username= :identifier')
        ->setParameter('identifier',$userNameOrEmail)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
    /**
     * Récupère un utilisateur en fonction de son adresse email.
     * 
     * Cette méthode permet de rechercher un utilisateur par son email.
     *
     * @param string $email : L'email de l'utilisateur à rechercher.
     * @return User|null : Un `User` si un utilisateur correspond, sinon `null`.
     */
    public function findUserByEmail(string $email):?User{
        return $this->createQueryBuilder('u')
        ->where('u.email = :identifier')
        ->setParameter('identifier',$email)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
    /**
     * Récupère le nombre total d'utilisateurs.
     * 
     * Cette méthode permet de compter le nombre d'utilisateurs dans la base de données.
     *
     * @return int : Le nombre total d'utilisateurs dans la base de données.
     */
    public function getTotalUsers():int{
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    /**
     * Récupère les rôles d'un utilisateur par son identifiant.
     *
     * @param int $id : L'identifiant de l'utilisateur.
     * @return array : Un tableau des rôles associés à l'utilisateur.
     */
    private function getUserRoles(int $id){
        $res = $this->createQueryBuilder('u')
            ->select()
            ->where('u.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getOneOrNullResult();
        return $res->getRoles();
    }
    /**
     * Supprime un utilisateur de la base de données.
     * 
     * Cette méthode vérifie d'abord qu'il n'a pas le rôle `ROLE_ADMIN` puis le supprime.
     *
     * @param int $id : L'identifiant de l'utilisateur à supprimer.
     */
    public function deleteUser(int $id){
        if(!in_array("ROLE_ADMIN",$this->getUserRoles($id))){ //verification pour ne pas pouvoir supprimer un admin
            $this->createQueryBuilder('u')
            ->delete()
            ->where('u.id = :identifier')
            ->setParameter('identifier',$id)
            ->getQuery()
            ->execute();
        }
        
    }
}
