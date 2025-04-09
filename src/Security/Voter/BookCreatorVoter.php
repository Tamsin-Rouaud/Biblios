<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * BookCreatorVoter
 *
 * Ce voter est destiné à vérifier si l'utilisateur actuellement authentifié est le créateur (auteur)
 * d'un "Book" (ou de l'entité correspondante). Il permet de contrôler l'accès à certaines actions,
 * par exemple pour l'édition ou la visualisation.
 *
 * Remarque : Ici, l'implémentation attend que le sujet ($subject) soit une instance de User, 
 * mais le commentaire suggère que c'est un Book. Normalement, le sujet devrait être une instance
 * de Book (ou de l'entité concernée) pour pouvoir appeler la méthode getCreatedBy().
 */
final class BookCreatorVoter extends Voter
{
    // Constantes représentant des actions possibles (bien que non utilisées dans supports() ici).
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    /**
     * Méthode supports
     *
     * Cette méthode détermine si le voter doit s'occuper de l'attribut et du sujet passés en paramètres.
     *
     * @param string $attribute L'attribut (ou l'action) à vérifier.
     * @param mixed  $subject   L'objet sur lequel l'accès doit être contrôlé.
     *
     * @return bool Retourne true si l'attribut est "book.is_creator" et que le sujet est une instance de User.
     *
     * Remarque : On attend ici que le sujet soit une instance de User, ce qui semble en contradiction avec le commentaire
     * indiquant "Book $subject". Dans une implémentation classique, $subject devrait être un Book, et on vérifierait alors
     * que l'utilisateur authentifié est bien celui qui a créé ce Book (via $subject->getCreatedBy()).
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Vérifie que l'attribut demandé est bien "book.is_creator" et que le sujet est une instance de User.
        return 'book.is_creator' === $attribute && $subject instanceof User;
    }

    /**
     * Méthode voteOnAttribute
     *
     * Cette méthode contient la logique de décision pour accorder ou refuser l'accès.
     *
     * @param string         $attribute L'attribut à vérifier.
     * @param mixed          $subject   L'objet sur lequel l'accès est contrôlé.
     * @param TokenInterface $token     Le token de sécurité représentant l'utilisateur actuellement authentifié.
     *
     * @return bool Retourne true si l'utilisateur est le créateur (auteur) de l'objet, false sinon.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Récupère l'utilisateur connecté depuis le token.
        $user = $token->getUser();

        // Si l'utilisateur n'est pas authentifié (ou n'est pas une instance de User), on refuse l'accès.
        if (!$user instanceof User) {
            return false;
        }

        /**
         * Ici, on s'attend normalement à ce que $subject soit une instance de Book.
         * Par exemple, on aurait :
         * 
         *   if (!$subject instanceof Book) {
         *       return false;
         *   }
         *
         * Puis on vérifierait que l'utilisateur connecté est bien le créateur du Book en comparant :
         *
         *   return $user === $subject->getCreatedBy();
         *
         * Dans ce code, même si le type de $subject est vérifié dans supports() comme User,
         * le commentaire laisse penser qu'on devrait utiliser une entité Book. 
         * Assure-toi que le type du sujet est bien celui attendu.
         */

        // Vérifie que l'utilisateur connecté est le même que celui qui a créé le "Book".
        // Remarque : il manque probablement des parenthèses ici pour appeler la méthode getCreatedBy(),
        // c'est-à-dire : $subject->getCreatedBy().
        return $user === $subject->getCreatedBy;
    }
}
