<?php
namespace App\Controller;

use Cake\Controller\Controller;
// use Cake\Http\Response;
use Firebase\JWT\JWT;

class AuthController extends Controller
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
            'car_seat_nb' => $data['carSeatNb'] ?? null
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
