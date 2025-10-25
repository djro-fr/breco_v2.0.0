<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        // CORS Headers
        $this->response = $this->response
            ->withHeader('Access-Control-Allow-Origin', env('CORS_ORIGIN','http://localhost:3001'))
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withType('application/json');

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
                    'error' => 'Email et mot de passe requis'
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
                    'error' => 'Email ou mot de passe incorrect'
                ]));
        }

        // Generate the JWT token
        $token = JWT::encode(
            [
                'sub' => $user->id,
                'email' => $user->email,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60) // 7 jours
            ],
            env('JWT_SECRET', 'your-secret-key'),
            'HS256'
        );

        return $this->response->withStringBody(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'createdAt' => $user->createdAt ? $user->createdAt->format('Y-m-d H:i:s') : null,
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
                    'error' => 'Email déjà utilisé'
                ]));
        }

        // Create a new user
        $user = $usersTable->newEntity([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'driver' => $data['driver'] ?? false,
            'gender' => $data['gender'] ?? null,
            'zipCode' => $data['zipCode'] ?? null,
            'town' => $data['town'] ?? null,
            'carModel' => $data['carModel'] ?? null,
            'carColor' => $data['carColor'] ?? null,
            'carSeatNb' => $data['carSeatNb'] ?? null
        ]);

        if (!$usersTable->save($user)) {
            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'error' => 'Erreur lors de l\'inscription'
                ]));
        }

        // Generate the JWT token
        $token = JWT::encode(
            [
                'sub' => $user->id,
                'email' => $user->email,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ],
            env('JWT_SECRET', 'your-secret-key'),
            'HS256'
        );

        return $this->response->withStringBody(json_encode([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'driver' => $user->driver,
                'createdAt' => $user->createdAt ? $user->createdAt->format('Y-m-d H:i:s') : null,
                'gender' => $user->gender,
                'zipCode' => $user->zipCode,
                'town' => $user->town,
                'carModel' => $user->carModel,
                'carColor' => $user->carColor,
                'carSeatNb' => $user->carSeatNb,
            ]
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
                'firstName' => $user->firstName,
                'driver' => $user->driver,
                'lastName' => $user->lastName,
                'createdAt' => $user->createdAt->format('Y-m-d H:i:s'),
                'gender' => $user->gender,
                'zipCode' => $user->zipCode,
                'town' => $user->town,
                'carModel' => $user->carModel,
                'carColor' => $user->carColor,
                'carSeatNb' => $user->carSeatNb,
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
