<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de l'authentification des User.
 * 
 * Gère l'affichage du formulaire de connexion et la déconnexion
 */
final class SecurityController extends AbstractController
{
    /**
     * Page de connexion
     * 
     * - Affiche le formulaire
     * - Si l'utilisateur est déjà connecté, renvoi vers l'accueil
     * - Affiche les erreurs de connexion
     * 
     * @param AuthencationUtils $authenticationUtils
     * 
     * @return Response Réponse HTTP contenant la page de connexion
     */
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si un User est déjà connecté, pas de formulaire
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Récupération de l'erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupèration des identifiants (email, name) saisi par User
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Route de déconnexion
     * 
     * @return void
     * 
     * @throws \LogicException 
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode reste vide car elle est intercepté par le firewall');
    }
}