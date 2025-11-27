<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Gère la redirection après une connexion réussie.
 * 
 * - Si l'utilisateur est un administrateur (ROLE_ADMIN), il est redirigé vers le back-office.
 * - Sinon, il est redirigé vers la page d'accueil du site.
 */
final class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var RouterInterface Service de routage pour générer les URLs de redirection.
     */
    private RouterInterface $router;

    /**
     * Constructeur du handler.
     * 
     * @param RouterInterface $router Service de routage.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Méthode appelée automatiquement lorsque l'utilisateur s'est connecté avec succès.
     * 
     * @param Request $request Requête HTTP.
     * @param TokenInterface $token Jeton d'authentification de l'utilisateur.
     * 
     * @return RedirectResponse Réponse de redirection vers la page appropriée.
     * 
     * @throws \LogicException Si l'objet utilisateur récupéré dans le token n'est pas une instance de User.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        //Vérification de sécurité
        if (!$user instanceof User) {
            throw new \LogicException('L’utilisateur authentifié n’est pas une instance de App\Entity\User.');
        }

        // Vérifie si l'utilisateur a le rôle d'administrateur
        if ($user->isAdmin()) {
            // Redirection vers le back-office
            return new RedirectResponse($this->router->generate('app_admin'));
        } else {
            // Redirection vers la page d'accueil du site
            return new RedirectResponse($this->router->generate('app_home'));
        }
    }
}