<?php
// backend/breco/src/Service/EmailService.php
declare(strict_types=1);

namespace App\Service;

use Cake\Mailer\Mailer;
use Cake\Log\Log;

class EmailService
{
    /**
     * Send a verification email to the user
     *
     * @param string $email The recipient email address
     * @param string $token The verification token
     * @param string $userName The first name of the user
     * @return bool True if the email was sent successfully, false otherwise
     */
    public function sendVerificationEmail(string $email, string $token, string $userName): bool
    {
        try {
            // Build the verification URL (points to frontend)
            $verificationUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/auth/verify-email/' . $token;

            $mailer = new Mailer('default');
            $mailer
                ->setTo($email)
                ->setSubject('Confirmez votre inscription sur Breco')
                ->setViewVars([
                    'userName' => $userName,
                    'verificationUrl' => $verificationUrl,
                    'token' => $token
                ])
                ->viewBuilder()
                ->setTemplate('verification')
                ->setLayout('default');

            $mailer->deliver();

            Log::info('Email de vérification envoyé', ['email' => $email]);
            return true;
        } catch (\Exception $e) {
            error_log('=== EMAIL ERROR DETAILS ===');
            error_log('Message: ' . $e->getMessage());
            error_log('File: ' . $e->getFile());
            error_log('Line: ' . $e->getLine());
            error_log('Trace: ' . $e->getTraceAsString());

            Log::error('Erreur lors de l\'envoi de l\'email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
