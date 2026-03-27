<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\Auth\AuthService;
use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterRequest;
use Cake\Http\Response;
use SwaggerBake\Lib\Attribute as Swag;

/**
 * Auth Controller
 *
 * Handles authentication operations
 */
class AuthController extends AppController
{

    private const CONTENT_TYPE_JSON = 'application/json';
    private const ERROR_SERVER = 'Erreur serveur';

    private AuthService $authService;

    public function initialize(): void
    {
        parent::initialize();
        $this->authService = new AuthService();
    }

    /**
     * Handle JSON body parsing
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->response = $this->response->withType(self::CONTENT_TYPE_JSON);

        // Handle OPTIONS for CORS
        if ($this->request->getMethod() === 'OPTIONS') {
            $this->autoRender = false;
            return;
        }

        // Parse JSON body
        if ($this->request->is('json')) {
            $this->request = $this->request->withParsedBody(
                json_decode((string)$this->request->getBody(), true) ?? []
            );
        }
    }

    /**
     * Build a JSON error response from an exception
     */
    private function buildErrorResponse(\Exception $e, string $key = 'error'): Response
    {
        if ($e instanceof \RuntimeException && $e->getCode() > 0) {
            $status = $e->getCode();
        } elseif ($e instanceof \InvalidArgumentException) {
            $status = 422;
        } else {
            $this->log($e->getMessage(), 'error');
            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([$key => self::ERROR_SERVER]));
        }

        return $this->response
            ->withStatus($status)
            ->withStringBody(json_encode([$key => $e->getMessage()]));
    }

    /**
     * 1. Register new user
     * POST /api/auth/register
     */
    #[Swag\OpenApiRequestBody(mimeTypes: [self::CONTENT_TYPE_JSON], required: true, description: 'Registration data')]
    #[Swag\OpenApiForm(name: 'email', type: 'string', description: 'User email', example: 'user@example.com', isRequired: true)]
    #[Swag\OpenApiForm(name: 'phone', type: 'string', description: 'Phone number', example: '0612345678', isRequired: true)]
    #[Swag\OpenApiForm(name: 'password', type: 'string', description: 'User password', example: 'Password123', isRequired: true)]
    #[Swag\OpenApiForm(name: 'firstName', type: 'string', description: 'First name', example: 'Jean', isRequired: true)]
    #[Swag\OpenApiForm(name: 'lastName', type: 'string', description: 'Last name', example: 'Dupont', isRequired: true)]
    #[Swag\OpenApiForm(name: 'driver', type: 'boolean', description: 'Driver ?', example: 'false', isRequired: true)]
    #[Swag\OpenApiForm(name: 'gender', type: 'string', description: 'Gender: Homme, Femme, Ne pas dire', example: 'Homme', isRequired: false)]
    public function register(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            $data = $this->request->getData();

            $registerRequest = new RegisterRequest(
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['firstName'] ?? '',
                $data['lastName'] ?? '',
                $data['phone'] ?? '',
                $data['gender'] ?? null,
                isset($data['age']) ? (int)$data['age'] : null
            );

            $result = $this->authService->register($registerRequest);

            return $this->response
                ->withStatus(201)
                ->withStringBody(json_encode($result));
        } catch (\Exception $e) {
            return $this->buildErrorResponse($e);
        }
    }

    /**
     * 2. Verify email with token
     * GET /api/auth/verify-email/{token}
     */
    public function verifyEmail($token = null): Response
    {
        $this->request->allowMethod(['get']);

        try {
            if (!$token) {
                throw new \InvalidArgumentException('Token demandé');
            }

            $result = $this->authService->verifyEmail($token);

            return $this->response
                ->withStatus(200)
                ->withStringBody(json_encode($result));
        }
        catch (\Exception $e) {
            return $this->buildErrorResponse($e);
        }
    }

    /**
     * 3. Login user
     * POST /api/auth/login
     */
    #[Swag\OpenApiRequestBody(mimeTypes: [self::CONTENT_TYPE_JSON], required: true, description: 'Login credentials')]
    #[Swag\OpenApiForm(name: 'email', type: 'string', description: 'User email', example: 'user@example.com', isRequired: true)]
    #[Swag\OpenApiForm(name: 'password', type: 'string', description: 'User password', example: 'Password123', isRequired: true)]
    public function login(): Response
    {
        $this->request->allowMethod(['post']);

        try {
            $data = $this->request->getData();

            $loginRequest = new LoginRequest(
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

            $result = $this->authService->login($loginRequest);

            return $this->response
                ->withStatus(200)
                ->withStringBody(json_encode($result));
        }
        catch (\Exception $e) {
            return $this->buildErrorResponse($e);
        }
    }

    /**
     * 4. Verify JWT token
     * GET /api/auth/verify
     */
    #[Swag\OpenApiSecurity(name: 'bearerAuth')]
    public function verify(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $token = $this->request->getHeaderLine('Authorization');

            if (empty($token)) {
                throw new \InvalidArgumentException('Token demandé');
            }

            // Remove "Bearer " prefix
            $token = str_replace('Bearer ', '', $token);

            $user = $this->authService->verifyToken($token);

            return $this->response
                ->withStatus(200)
                ->withStringBody(json_encode($user));
        }
        catch (\Exception $e) {
            return $this->buildErrorResponse($e);
        }
    }

    /**
     * 5. Logout (client-side token removal)
     * POST /api/auth/logout
     */
    public function logout(): Response
    {
        $this->request->allowMethod(['post']);

        return $this->response
            ->withStatus(200)
            ->withStringBody(json_encode([
                'message' => 'Déconnexion réussie'
            ]));
    }

    /**
     * Handle OPTIONS requests for CORS
     */
    public function options()
    {
        $this->autoRender = false;
        return $this->response->withStatus(200)->withStringBody('');
    }
}
