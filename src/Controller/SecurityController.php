<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 * Ce contrôleur gère l'authentification des utilisateurs, en affichant le formulaire de connexion
 * et en traitant la déconnexion. La méthode de déconnexion n'est jamais exécutée directement,
 * car elle est interceptée par le système de sécurité configuré (firewall).
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et gère la récupération des erreurs d'authentification.
     *
     * Cette méthode utilise l'utilitaire AuthenticationUtils pour obtenir :
     * - La dernière erreur d'authentification (s'il y en a une).
     * - Le dernier nom d'utilisateur saisi par l'utilisateur (afin de préremplir le champ du formulaire).
     *
     * @param AuthenticationUtils $authenticationUtils L'outil d'authentification permettant de récupérer
     *                                                   les informations liées à la dernière tentative de connexion.
     *
     * @return Response La réponse HTTP contenant le rendu du formulaire de connexion.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion, par exemple une erreur de mot de passe ou un identifiant incorrect.
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur saisi par l'utilisateur pour le réafficher dans le formulaire.
        $lastUsername = $authenticationUtils->getLastUsername();

        // Retourne le rendu du template de connexion en lui passant le dernier nom d'utilisateur et l'erreur éventuelle.
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Prévient l'utilisateur en affichant le dernier nom d'utilisateur saisi.
            'error' => $error,               // Affiche le message d'erreur en cas d'échec d'authentification.
        ]);
    }

    /**
     * Point d'entrée pour la déconnexion de l'utilisateur.
     *
     * IMPORTANT : Cette méthode ne doit jamais être appelée directement car la déconnexion
     * est gérée par le firewall de Symfony. Le système de sécurité intercepte la route '/logout'
     * et effectue la déconnexion de l'utilisateur.
     *
     * @return void
     *
     * @throws \LogicException Cette exception est levée pour indiquer que ce code ne doit pas être exécuté.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette exception est volontairement levée afin de rappeler que la déconnexion est gérée
        // par le système de sécurité (firewall) et que ce point d'entrée ne doit pas contenir de logique.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
