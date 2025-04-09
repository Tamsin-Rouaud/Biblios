<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class RegistrationController
 *
 * Ce contrôleur gère la création de nouveaux utilisateurs (inscription) depuis l'administration.
 * Seuls les utilisateurs possédant le rôle ROLE_ADMIN ont accès à cette fonctionnalité.
 */
class RegistrationController extends AbstractController
{
    // Restreint l'accès à cette méthode aux utilisateurs ayant le rôle "ROLE_ADMIN"
    #[IsGranted('ROLE_ADMIN')]
    // Définition de la route pour la création d'un nouvel utilisateur dans le back-office (administration).
    // URL : /admin/user/new
    #[Route('admin/user/new', name: 'app_admin_user_new')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de l'entité User.
        $user = new User();

        // Création du formulaire d'inscription à partir de la classe RegistrationFormType,
        // en liant le formulaire à l'objet $user.
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Récupération et traitement des données de la requête HTTP.
        // Cela permet de remplir le formulaire avec les données soumises (le cas échéant).
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et si les données sont valides.
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            // Récupère le mot de passe en clair saisi par l'utilisateur.
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode (hachage) le mot de passe en clair et le stocke dans l'objet User.
            // La méthode hashPassword() utilise l'algorithme défini dans la configuration de sécurité.
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Prépare l'objet User à être sauvegardé en base de données.
            $entityManager->persist($user);
            // Exécute les requêtes SQL pour enregistrer l'utilisateur.
            $entityManager->flush();

            // Une fois l'utilisateur enregistré, vous pouvez effectuer d'autres actions (ex. envoyer un email de confirmation)
            // et rediriger vers une autre page. Ici, nous redirigeons vers l'index des livres.
            return $this->redirectToRoute('app_admin_user_new');
        }

        // Si le formulaire n'est pas soumis ou présente des erreurs,
        // affiche le template d'inscription en lui passant le formulaire.
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
