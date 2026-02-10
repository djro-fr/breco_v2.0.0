<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\User\UserService;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Users Controller
 *
 * Handles user-related operations
 * Requires JWT authentication
 */
class UsersController extends AppController
{
    use \App\Controller\Trait\JwtAuthTrait;

    private UserService $userService;

    public function initialize(): void
    {
        parent::initialize();
        $this->userService = new UserService();
    }

    /**
     * Before filter - require JWT authentication
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Handle CORS preflight
        if ($this->request->getMethod() === 'OPTIONS') {
            $this->autoRender = false;
            return $this->response->withStatus(200);
        }

        // Require JWT authentication
        $authResponse = $this->requireAuth();
        if ($authResponse) {
            return $authResponse;
        }
    }

    /**
     * List all users
     * GET /api/users
     */
    public function index(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $users = $this->userService->listAll();

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $users,
                    'count' => count($users),
                    'requestedBy' => $this->currentUser->email
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withType('application/json')
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Server error'
                ]));
        }
    }
}
