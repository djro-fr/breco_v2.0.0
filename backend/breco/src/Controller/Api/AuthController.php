<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\Auth\AuthService;
use App\Dto\Auth\LoginRequest;
use App\Dto\Auth\RegisterRequest;
use Cake\Http\Response;

/**
 * Auth Controller
 *
 * Handles authentication operations
 */
class AuthController extends AppController
{
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

        $this->response = $this->response->withType('application/json');

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
     * Login user
     * POST /api/auth/login
     */
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

        } catch (\InvalidArgumentException $e) {
            return $this->response
                ->withStatus(422)
                ->withStringBody(json_encode([
                    'error' => $e->getMessage()
                ]));

        } catch (\RuntimeException $e) {
            return $this->response
                ->withStatus($e->getCode() ?: 500)
                ->withStringBody(json_encode([
                    'error' => $e->getMessage()
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'error' => 'Server error'
                ]));
        }
    }

    /**
     * Register new user
     * POST /api/auth/register
     */
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

        } catch (\InvalidArgumentException $e) {
            return $this->response
                ->withStatus(422)
                ->withStringBody(json_encode([
                    'error' => $e->getMessage()
                ]));

        } catch (\RuntimeException $e) {
            return $this->response
                ->withStatus($e->getCode() ?: 500)
                ->withStringBody(json_encode([
                    'error' => $e->getMessage()
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'error' => 'Server error'
                ]));
        }
    }

    /**
     * Verify email with token
     * GET /api/auth/verify-email/{token}
     */
    public function verifyEmail($token = null): Response
    {
        $this->request->allowMethod(['get']);

        try {
            if (!$token) {
                throw new \InvalidArgumentException('Token is required');
            }

            $result = $this->authService->verifyEmail($token);

            return $this->response
                ->withStatus(200)
                ->withStringBody(json_encode($result));

        } catch (\InvalidArgumentException $e) {
            return $this->response
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));

        } catch (\RuntimeException $e) {
            return $this->response
                ->withStatus($e->getCode() ?: 500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Server error'
                ]));
        }
    }

    /**
     * Verify JWT token
     * GET /api/auth/verify
     */
    public function verify(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $token = $this->request->getHeaderLine('Authorization');

            if (empty($token)) {
                throw new \RuntimeException('Token required', 401);
            }

            // Remove "Bearer " prefix
            $token = str_replace('Bearer ', '', $token);

            $user = $this->authService->verifyToken($token);

            return $this->response
                ->withStatus(200)
                ->withStringBody(json_encode($user));

        } catch (\RuntimeException $e) {
            return $this->response
                ->withStatus($e->getCode() ?: 401)
                ->withStringBody(json_encode([
                    'error' => $e->getMessage()
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withStatus(401)
                ->withStringBody(json_encode([
                    'error' => 'Invalid token'
                ]));
        }
    }

    /**
     * Logout (client-side token removal)
     * POST /api/auth/logout
     */
    public function logout(): Response
    {
        $this->request->allowMethod(['post']);

        return $this->response
            ->withStatus(200)
            ->withStringBody(json_encode([
                'message' => 'Logout successful'
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
