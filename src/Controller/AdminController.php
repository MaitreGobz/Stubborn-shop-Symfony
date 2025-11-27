<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour les pages d'administration.
 * 
 * Accessible uniquement aux utilisateurs avec le rôle ROLE_ADMIN.
 * 
 * @see User::isAdmin()
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class AdminController extends AbstractController
{
    /**
     * Page d'accueil du back-office.
     * 
     * Route : /admin/
     * 
     * @return Response Page HTML de l'admin.
     */
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            // 'products' => ...
        ]);
    }
}