<?php

namespace App\Controller;

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
     * @return Response La réponse HTTP contenant la vue de succès.
     */
    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function success(): Response
    {
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
        return $this->render('payment/cancel.html.twig');
    }
}