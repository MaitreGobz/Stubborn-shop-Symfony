<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des paiements.
 * Gère les pages de succès et d'annulation après le paiement.
 */
final class PaymentController extends AbstractController
{
    /**
     * Page de succès après un paiement réussi.
     * 
     * Route : /payment/success
     * 
     * @param CartService $cartService Service de gestion du panier.
     * 
     * @return Response La réponse HTTP contenant la vue de succès.
     */
    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function success(CartService $cartService): Response
    {
        // Empty the cart after a successful payment
        $cartService->clear();

        //Success Flash Message
        $this->addFlash('success', 'Paiement effectué avec succès ! Merci pour votre commande.');

        return $this->render('payment/success.html.twig');
    }

    /**
     * Page d'annulation après un paiement échoué ou annulé.
     * 
     * Route : /payment/cancel
     * 
     * @return Response La réponse HTTP contenant la vue d'annulation.
     */
    #[Route('/payment/cancel', name: 'payment_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        //Cancel Flash Message
        $this->addFlash('error', 'Le paiement a été annulé ou a échoué. Veuillez réessayer.');
        
        return $this->render('payment/cancel.html.twig');
    }
}