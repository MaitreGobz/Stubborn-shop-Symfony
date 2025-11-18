<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    /**
     * Page d'inscription
     * 
     * - Affiche le formulaire d'inscription
     * - Permet de valider l'inscription et redirige vers la page de login
     */
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }
}
