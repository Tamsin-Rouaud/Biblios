<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor_index', methods: ['GET'])]
    public function index(Request $request, EditorRepository $repository): Response
    {
        $editors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->createQueryBuilder('e')),
            $request->query->get('page', 1),
            10
        );

        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editors
        ]);
    }

    #[IsGranted('ROLE_AJOUT_DE_LIVRE', 'ROLE_EDITION_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    // On impose que l'utilisateur doit posséder le rôle 'ROLE_AJOUT_DE_LIVRE' pour accéder à ces routes.
// Cela signifie que toute personne souhaitant créer ou modifier un éditeur doit avoir ce rôle.
#[IsGranted('ROLE_AJOUT_DE_LIVRE')]

// Définition de la route pour créer un nouvel éditeur.
// La méthode GET affiche le formulaire, et la méthode POST permet de le soumettre.
#[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]

// Définition de la route pour modifier un éditeur existant.
// La contrainte 'id' doit être un nombre (\d+). Les méthodes GET et POST sont autorisées.
#[IsGranted('ROLE_EDITION_DE_LIVRE')]
#[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function new(?Editor $editor, Request $request, EntityManagerInterface $manager): Response
{
    // Si un objet Editor est fourni (c'est-à-dire qu'on est en mode édition),
    // on vérifie que l'utilisateur a le droit d'éditer un éditeur en possédant le rôle 'ROLE_EDITION_DE_LIVRE'.
    // Sinon, un accès est refusé.
    if ($editor) {
        $this->denyAccessUnlessGranted('ROLE_EDITION_DE_LIVRE');
    }

    // Si l'objet $editor est null (cas de la création), on crée un nouvel objet Editor.
    $editor ??= new Editor();

    // Création du formulaire à partir du type EditorType, en liant l'objet $editor.
    // Le formulaire sera utilisé pour saisir/modifier les informations de l'éditeur.
    $form = $this->createForm(EditorType::class, $editor);

    // Traitement de la requête HTTP afin de remplir le formulaire avec les données envoyées (POST)
    // et de gérer la soumission.
    $form->handleRequest($request);

    // Vérifie si le formulaire a été soumis et si les données sont valides.
    if ($form->isSubmitted() && $form->isValid()) {
        // Si le formulaire est valide, l'objet $editor est persisté en base de données.
        $manager->persist($editor);
        $manager->flush();

        // Après la sauvegarde, redirige vers la liste des éditeurs.
        return $this->redirectToRoute('app_admin_editor_index');
    }

    // Si le formulaire n'est pas soumis ou n'est pas valide,
    // affiche le template du formulaire pour créer ou éditer un éditeur.
    return $this->render('admin/editor/new.html.twig', [
        'form' => $form, // Envoie le formulaire au template pour être affiché.
    ]);
}


    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
}