<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Contrôleur pour tester l'envoi d'emails via MailHog.
 */
class TestEmailController extends AbstractController
{
    /** 
     * Envoie un email de test via MailHog.
     *
     * @Route("/test-email", name="test_email")
     * @param MailerInterface $mailer Le service de messagerie pour envoyer les emails.
     * @return \Symfony\Component\HttpFoundation\JsonResponse La réponse JSON indiquant le succès ou l'échec de l'envoi.
     */
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer)
    {
        try {
            $email = (new Email())
                ->from('test@example.com')
                ->to('recipient@example.com')
                ->subject('Test Email')
                ->text('This is a test email sent via MailHog and Symfony.');

            $mailer->send($email);

            return $this->json(['success' => true, 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}