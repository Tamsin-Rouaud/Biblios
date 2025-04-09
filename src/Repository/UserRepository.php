<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * UserRepository
 *
 * Ce repository est utilisé pour interagir avec l'entité User dans la base de données.
 * Il étend ServiceEntityRepository pour bénéficier de méthodes génériques (find, findAll, etc.)
 * et implémente PasswordUpgraderInterface pour permettre la mise à jour automatique du hash des mots de passe.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructeur du repository.
     *
     * @param ManagerRegistry $registry Le registre des managers qui permet de récupérer l'EntityManager.
     */
    public function __construct(ManagerRegistry $registry)
    {
        // Le parent est initialisé avec le ManagerRegistry et la classe de l'entité gérée (User::class).
        parent::__construct($registry, User::class);
    }

    /**
     * Méthode utilisée pour mettre à jour (rehacher) automatiquement le mot de passe de l'utilisateur.
     *
     * Cette méthode est appelée lorsqu'une ré-hachage du mot de passe est nécessaire (par exemple, après une modification
     * de l'algorithme de hachage ou lors d'une ré-authentification).
     *
     * @param PasswordAuthenticatedUserInterface $user             L'utilisateur dont le mot de passe doit être mis à jour.
     * @param string                               $newHashedPassword Le nouveau mot de passe déjà haché.
     *
     * @throws UnsupportedUserException Si l'utilisateur passé en paramètre n'est pas une instance de User.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // Vérifie que l'utilisateur est bien une instance de l'entité User.
        if (!$user instanceof User) {
            // Si ce n'est pas le cas, une exception est levée pour signaler que cet utilisateur n'est pas supporté.
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        // Met à jour le mot de passe de l'utilisateur avec le nouveau hash.
        $user->setPassword($newHashedPassword);
        // Persiste l'utilisateur modifié pour préparer la mise à jour dans la base de données.
        $this->getEntityManager()->persist($user);
        // Exécute les requêtes SQL nécessaires pour sauvegarder la modification.
        $this->getEntityManager()->flush();
    }

    // Ces méthodes commentées sont des exemples de requêtes personnalisées que vous pouvez activer
    // pour rechercher des utilisateurs selon certains critères.
    //
    // Exemple pour récupérer un ensemble d'utilisateurs en fonction d'un champ particulier :
    //
    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('u.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    //
    // Exemple pour récupérer un utilisateur unique en fonction d'un critère :
    //
    // public function findOneBySomeField($value): ?User
    // {
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
