<?php
declare(strict_types=1);

namespace App\Service\Auth;

use App\Repository\UserRepository;
use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterRequest;
use App\Service\EmailService;
use Firebase\JWT\JWT;
use Cake\I18n\DateTime;

use App\Exception\AuthenticationException;
use App\Exception\EmailNotVerifiedException;
use App\Exception\EmailAlreadyInUseException;
use App\Exception\VerificationException;
use App\Exception\TooManyAttemptsException;

class AuthService
{
    private UserRepository $userRepository;
    private EmailService $emailService;

    public function __construct(
        ?UserRepository $userRepository = null,
        ?EmailService $emailService = null
    ) {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->emailService = $emailService ?? new EmailService();
    }

    /**
     * Login user and generate JWT token
     *
     * @param LoginRequest $request
     * @return array ['token' => string, 'user' => array]
     * @throws \RuntimeException
     */
    public function login(LoginRequest $request): array
    {
        // Check for too many failed attempts
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if ($this->userRepository->countRecentAttempts($request->getEmail(), $ipAddress) >= 5) {
            throw new TooManyAttemptsException();
        }

        $user = $this->userRepository->findByEmail($request->getEmail());

        if (!$user || !password_verify($request->getPassword(), $user['password'])) {
            $this->userRepository->recordFailedAttempt($request->getEmail(), $ipAddress);
            throw new AuthenticationException('E-mail ou mot de passe incorrect');
        }

        if (!$user['email_verified']) {
            throw new EmailNotVerifiedException('Veuillez vérifier votre adresse e-mail avant de vous connecter');
        }

        $token = $this->generateToken($user);

        return [
            'token' => $token,
            'user' => $this->formatUser($user)
        ];
    }

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return array ['success' => bool, 'message' => string, 'requiresVerification' => bool]
     * @throws \RuntimeException
     */
    public function register(RegisterRequest $request): array
    {
        // Check if email exists
        if ($this->userRepository->emailExists($request->getEmail())) {
            throw new EmailAlreadyInUseException('Cette adresse e-mail est déjà utilisée');
        }

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        $expiresAt = new DateTime('+24 hours');

        // Prepare user data
        $userData = $request->toArray();
        $userData['email_verified'] = false;
        $userData['verification_token'] = $verificationToken;
        $userData['verification_token_expires'] = $expiresAt;

        // Create user
        $user = $this->userRepository->create($userData);

        // Send verification email
        $emailSent = $this->emailService->sendVerificationEmail(
            $user['email'],
            $verificationToken,
            $user['first_name']
        );

        if (!$emailSent) {
            return [
                'success' => true,
                'message' => 'Inscription réussie, mais l\'e-mail de vérification n\'a pas pu être envoyé. Veuillez contacter le support.',
                'requiresVerification' => true
            ];
        }

        return [
            'success' => true,
            'message' => 'Inscription réussie ! Un e-mail de vérification a été envoyé à votre adresse.',
            'requiresVerification' => true
        ];
    }

    /**
     * Verify user email with token
     *
     * @param string $token
     * @return array ['success' => bool, 'message' => string]
     * @throws \RuntimeException
     */
    public function verifyEmail(string $token): array
    {
        $user = $this->userRepository->findByVerificationToken($token);

        if (!$user) {
            throw new VerificationException('Lien invalide ou expiré');
        }

        if (!$this->userRepository->verifyEmail($user['id'])) {
            throw new VerificationException('La vérification a échoué', 500);
        }

        return [
            'success' => true,
            'message' => 'E-mail vérifié avec succès ! Vous pouvez maintenant vous connecter.'
        ];
    }

    /**
     * Verify JWT token and return user data
     *
     * @param string $token
     * @return array User data
     * @throws \RuntimeException
     */
    public function verifyToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key($this->getJwtSecret(), 'HS256'));

            $user = $this->userRepository->findById($decoded->sub);

            if (!$user) {
                throw new AuthenticationException('Utilisateur introuvable');
            }

            return $this->formatUser($user);

        } catch (\Exception $e) {
            throw new AuthenticationException('Token invalide');
        }
    }

    /**
     * Generate JWT token for user
     *
     * @param array $user
     * @return string
     */
    private function generateToken(array $user): string
    {
        return JWT::encode(
            [
                'sub' => $user['id'],
                'email' => $user['email'],
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60) // 7 days
            ],
            $this->getJwtSecret(),
            'HS256'
        );
    }

    /**
     * Format user data for API response
     *
     * @param array $user
     * @return array
     */
    private function formatUser(array $user): array
    {
        return [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'gender' => $user['gender'] ?? null,
            'age' => $user['age'] ?? null,
            'createdAt' => isset($user['created']) ? $user['created']->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Get JWT secret from environment
     *
     * @return string
     */
    private function getJwtSecret(): string
    {
        return env('JWT_SECRET', 'your-secret-key');
    }
}
