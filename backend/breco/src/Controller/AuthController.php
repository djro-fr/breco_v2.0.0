<?php
// backend\breco\src\Controller\AuthController.php
namespace App\Controller;

use Firebase\JWT\JWT;
use App\Service\EmailService;
use Cake\I18n\DateTime; // A CakePHP class to manipulate dates/times in an immutable way, to create expiration times (24h)

class AuthController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->response = $this->response->withType('application/json');

        // Handle OPTIONS
        if ($this->request->getMethod() === 'OPTIONS') {
            $this->autoRender = false;
            return;
        }

        if ($this->request->is('json')) {
            $this->request = $this->request->withParsedBody(
                json_decode((string)$this->request->getBody(), true) ?? []
            );
        }
    }

    // Login
    public function login()
    {
        $this->request->allowMethod(['post']);
        $this->response = $this->response->withType('application/json');

        $data = $this->request->getData();

        // Validate data
        if (empty($data['email']) || empty($data['password'])) {
            return $this->response
                ->withStatus(422)
                ->withStringBody(json_encode([
                    'error' => 'E-mail et mot de passe requis'
                ]));
        }

        // Search for the user
        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->find()
            ->where(['email' => $data['email']])
            ->first();

        if (!$user || !password_verify($data['password'], $user->password)) {
            return $this->response
                ->withStatus(401)
                ->withStringBody(json_encode([
                    'error' => 'E-mail ou mot de passe incorrect'
                ]));
        }

        // Check if email is verified
        if (!$user->email_verified) {
            return $this->response
                ->withStatus(403)
                ->withStringBody(json_encode([
                    'error' => 'Veuillez vérifier votre adresse e-mail avant de vous connecter'
                ]));
        }

        // Generate the JWT token
        $token = JWT::encode(
            [
                'sub' => $user->id,
                'email' => $user->email,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60) // 7 days
            ],
            env('JWT_SECRET', 'your-secret-key'),
            'HS256'
        );

        return $this->response->withStringBody(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]));
    }

    // Register
    public function register()
    {
        $this->request->allowMethod(['post']);
        $this->response = $this->response->withType('application/json');

        $data = $this->request->getData();

        // Validate data
        if (empty($data['email']) || empty($data['password']) ||
            empty($data['firstName']) || empty($data['lastName']) ||
            empty($data['phone'])) {
            return $this->response
                ->withStatus(422)
                ->withStringBody(json_encode([
                    'error' => 'Tous les champs sont requis'
                ]));
        }

        $usersTable = $this->fetchTable('Users');

        // Check if the email already exists
        $existingUser = $usersTable->find()
            ->where(['email' => $data['email']])
            ->first();

        if ($existingUser) {
            return $this->response
                ->withStatus(422)
                ->withStringBody(json_encode([
                    'error' => 'E-mail déjà utilisé'
                ]));
        }

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));
        $expiresAt = new DateTime('+24 hours');

        // Create a new user
        $user = $usersTable->newEntity([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'driver' => $data['driver'] ?? false,
            'gender' => $data['gender'] ?? null,
            'zip_code' => $data['zipCode'] ?? null,
            'town' => $data['town'] ?? null,
            'car_model' => $data['carModel'] ?? null,
            'car_color' => $data['carColor'] ?? null,
            'car_seat_nb' => $data['carSeatNb'] ?? null,
            'email_verified' => false,
            'verification_token' => $verificationToken,
            'verification_token_expires' => $expiresAt
        ]);

        if (!$usersTable->save($user)) {
            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'error' => 'Erreur lors de l\'inscription'
                ]));
        }

        error_log('=== DEBUG EMAIL ===');
        error_log('User email: ' . $user->email);
        error_log('Token: ' . $verificationToken);
        error_log('First name: ' . $user->first_name);

        // Send verification email
        $emailService = new EmailService();
        $emailSent = $emailService->sendVerificationEmail(
            $user->email,
            $verificationToken,
            $user->first_name
        );

        error_log('Email sent result: ' . ($emailSent ? 'TRUE' : 'FALSE'));

        if (!$emailSent) {
            // User created but email could not be sent
            return $this->response
                ->withStatus(201)
                ->withStringBody(json_encode([
                    'success' => true,
                    'message' => 'Inscription réussie, mais l\'email de vérification n\'a pas pu être envoyé. Contactez le support.',
                    'requiresVerification' => true
                ]));
        }

        return $this->response
            ->withStatus(201)
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'Inscription réussie ! Un email de vérification a été envoyé à votre adresse.',
                'requiresVerification' => true
            ]));
    }

    // Verify email with token
    public function verifyEmail($token = null)
    {
        error_log("=== VERIFY EMAIL CALLED WITH TOKEN: " . ($token ?? 'NULL') . " ===");
        $this->request->allowMethod(['get']);
        $this->response = $this->response->withType('application/json');

        if (!$token) {
            return $this->response
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Token manquant'
                ]));
        }

        $usersTable = $this->fetchTable('Users');

        // Find user with this token and check if it's not expired
        $user = $usersTable->find()
            ->where([
                'verification_token' => $token,
                'verification_token_expires >' => new DateTime()
            ])
            ->first();

        if (!$user) {
            return $this->response
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Token invalide ou expiré'
                ]));
        }

        // Verify email
        $user->email_verified = true;
        $user->verification_token = null;
        $user->verification_token_expires = null;

        if (!$usersTable->save($user)) {
            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la vérification'
                ]));
        }

        return $this->response
            ->withStatus(200)
            ->withStringBody(json_encode([
                'success' => true,
                'message' => 'Email vérifié avec succès ! Vous pouvez maintenant vous connecter.'
            ]));
    }

    // Check the token
    public function verify()
    {
        $this->request->allowMethod(['get']);
        $this->response = $this->response->withType('application/json');

        $token = $this->request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->response
                ->withStatus(401)
                ->withStringBody(json_encode([
                    'error' => 'Token requis'
                ]));
        }

        // Remove "Bearer " from token
        $token = str_replace('Bearer ', '', $token);

        try {
            $decoded = JWT::decode($token, new \Firebase\JWT\Key(env('JWT_SECRET', 'your-secret-key'), 'HS256'));

            $usersTable = $this->fetchTable('Users');
            $user = $usersTable->get($decoded->sub);

            return $this->response->withStringBody(json_encode([
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'driver' => $user->driver,
                'createdAt' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                'gender' => $user->gender,
                'zipCode' => $user->zip_code,
                'town' => $user->town,
                'carModel' => $user->car_model,
                'carColor' => $user->car_color,
                'carSeatNb' => $user->car_seat_nb,
            ]));
        } catch (\Exception $e) {
            return $this->response
                ->withStatus(401)
                ->withStringBody(json_encode([
                    'error' => 'Token invalide'
                ]));
        }
    }

    // Logout
    public function logout()
    {
        $this->request->allowMethod(['post']);
        $this->response = $this->response->withType('application/json');

        // The logout is done on the frontend side (remove the token)
        return $this->response->withStringBody(json_encode([
            'message' => 'Déconnexion réussie'
        ]));
    }

    // Test
    public function test()
    {
        $this->response = $this->response->withType('application/json');
        return $this->response->withStringBody(json_encode(['message' => 'OK']));
    }

    public function options()
    {
        $this->autoRender = false;
        return $this->response->withStatus(200)->withStringBody('');
    }
}
