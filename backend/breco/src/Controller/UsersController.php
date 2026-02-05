<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Users Controller
*
* Handles user-related operations
* Requires JWT authentication for all actions
 */
class UsersController extends AppController
{
    use \App\Controller\Trait\JwtAuthTrait;

    /**
     * Before filter callback - require authentication
     */
    public function beforeFilter(EventInterface $event)
    {
        // Call parent beforeFilter in AppController
        parent::beforeFilter($event);

        // Handle CORS preflight OPTIONS requests
        if ($this->request->getMethod() === 'OPTIONS') {
            $this->autoRender = false;
            return $this->response->withStatus(200);
        }

        // Require JWT authentication for all actions in this controller
        $authResponse = $this->requireAuth();
        if ($authResponse) {
            return $authResponse;
        }
    }


    /**
     * List all users
     *
     * GET /api/users
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
    $this->request->allowMethod(['get']);
    $users = $this->fetchTable('Users')->find()->all();
    $result = [];
    foreach ($users as $user) {
        $result[] = [
            'id' => $user->id,
            "email" => $user->email,
            "last_name" => $user->last_name,
            "first_name" => $user->first_name,
            "phone" => $user->phone,
            "age" => $user->age,
            "gender" => $user->gender,
            "created" => $user->created,
            "modified" => $user->modified,
            "town_id" => $user->town_id
        ];
    }
    return $this->response
        ->withType('application/json')
        ->withStatus(200)
        ->withStringBody(json_encode([
            'success' => true,
            'data' => $result,
            'count' => count($result),
            'requested_by' => $this->currentUser->email
        ]));
    }
}
