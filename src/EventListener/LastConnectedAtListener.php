<?php

namespace App\EventListener;

// Importation de l'entité User pour vérifier le type de l'utilisateur
use App\Entity\User;
// Importation de l'EntityManagerInterface qui permet d'interagir avec la base de données
use Doctrine\ORM\EntityManagerInterface;
// Importation de l'attribut pour enregistrer le listener auprès de l'EventDispatcher
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
// Importation de la classe InteractiveLoginEvent qui représente l'événement de connexion interactive
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

// La classe LastConnectedAtListener est marquée comme "final" : elle ne peut pas être étendue.
final class LastConnectedAtListener
{
    // Le constructeur injecte l'EntityManagerInterface via l'injection de dépendance.
    // Ici, "private readonly" signifie que l'attribut $manager est accessible dans la classe et qu'il ne sera pas modifié après l'initialisation.
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    // Cet attribut indique que la méthode ci-dessous doit être appelée automatiquement lorsqu'un événement "security.interactive_login" est dispatché.
    // L'annotation #[AsEventListener(event: 'security.interactive_login')] permet d'enregistrer ce listener auprès de l'EventDispatcher.
    #[AsEventListener(event: 'security.interactive_login')]
    // La méthode onSecurityInteractiveLogin est exécutée lors de la connexion interactive de l'utilisateur.
    // Elle reçoit un objet InteractiveLoginEvent qui contient des informations sur la connexion.
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Récupération de l'utilisateur à partir du token d'authentification contenu dans l'événement.
        // getAuthenticationToken() renvoie le token, et getUser() renvoie l'objet utilisateur associé.
        $user = $event->getAuthenticationToken()->getUser();

        // On vérifie que l'objet retourné est bien une instance de l'entité User
        if ($user instanceof User) {
            // Mise à jour de la propriété "lastConnectedAt" de l'utilisateur avec la date et l'heure actuelles.
            // On utilise new \DateTimeImmutable() pour créer un objet date immuable représentant le moment de la connexion.
            $user->setLastConnectedAt(new \DateTimeImmutable());

            // On persiste la modification en base de données en appelant flush() sur l'EntityManager.
            // flush() applique toutes les modifications enregistrées sur l'entité.
            $this->manager->flush();
        }
    }
}
