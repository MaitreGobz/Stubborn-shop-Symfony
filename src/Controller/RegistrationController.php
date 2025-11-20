<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    /**
     * Service chargé de gérer l'envoi et la validation
     * des e-mails de confirmation d'inscription.
     */
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    /**
     * Affiche et traite le formulaire d'inscription utilisateur.
     *
     * - Crée un nouvel utilisateur avec le rôle ROLE_USER
     * - Hash le mot de passe saisi
     * - Envoie un e-mail de confirmation d'adresse
     *
     * @param Request                     $request          Requête HTTP contenant les données du formulaire.
     * @param UserPasswordHasherInterface $userPasswordHasher Service de hashage des mots de passe.
     * @param EntityManagerInterface      $entityManager    Gestionnaire d'entités Doctrine.
     *
     * @return Response Réponse HTTP (page d'inscription ou redirection).
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // rôle par défault pour les nouveaux comptes
            $user->setRoles(['ROLE_USER']);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@stubborn-shop.test', 'Stubborn Mail Bot'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $this->addFlash('succes', 'Votre compte a été créé. Un email de confirmaion vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form ->createView(),
        ]);
    }

     /**
     * Valide l'adresse e-mail d'un utilisateur à partir du lien reçu.
     *
     * Cette action :
     * - vérifie la signature du lien
     * - met à jour la propriété User::isVerified à true
     * - enregistre la modification en base
     *
     * L'utilisateur doit être authentifié pour accéder à cette route.
     *
     * @param Request            $request    Requête HTTP contenant le lien signé.
     * @param TranslatorInterface $translator Service de traduction des messages d'erreur.
     *
     * @return Response Réponse HTTP après vérification (redirection + message flash).
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
