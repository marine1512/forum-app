<?php

namespace App\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;

    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            $user->getId(),
            $user->getUserIdentifier(),
            ['id' => $user->getId()]
        );

        $email->context([
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'expiresAt' => $signatureComponents->getExpiresAt(),
            'username' => $user->getUsername(),
        ]);

    try {
        $this->mailer->send($email);
        error_log('Email envoyé avec succès.');
    } catch (\Exception $e) {
        // Log des erreurs
        error_log('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
    }
    }
}