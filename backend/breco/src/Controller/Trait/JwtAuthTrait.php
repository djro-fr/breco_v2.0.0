<?php
declare(strict_types=1);

namespace App\Controller\Trait;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Authentication Trait
 *
 * Methods to verify JWT tokens in CakePHP controllers.
 *
 */
trait JwtAuthTrait
{
    /**
     * Stores the authenticated user information
     * Contains the decoded JWT payload
     *
     * @var object|null
     */
    protected $currentUser = null;


    /**
     * Verify and decode the JWT token from the Authorization header
     *
     * @return object|null The decoded JWT payload or null if invalid
     */
    protected function verifyJwtToken()
    {
        // Reads the "Authorization: Bearer xxx" header and extracts the token
        $token = $this->request->getHeaderLine('Authorization');

        // If no Authorization header, no token
        if (empty($token)) {
            return null;
        }

        // Token arrives as "Bearer zkjHjukhkjh..."
        // Remove "Bearer " to keep just the token
        $token = str_replace('Bearer ', '', $token);

        try {
            // Decodes it with the secret key
            $decoded = JWT::decode(
                $token,
                new Key(
                    $this->getJwtSecret(),
                    'HS256'
                )
            );
            // If no exception, token is valid
            return $decoded;

        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * Get the JWT secret key from environment variables
     *
     * @return string The secret key
     */
    protected function getJwtSecret(): string
    {
        return env('JWT_SECRET', 'your-secret-key');
    }


    /**
     * Require authentication - return error if not authenticated
     * To use, call this method in beforeFilter() of controller.
     *
     * public function beforeFilter($event) {
     *     parent::beforeFilter($event);
     *     $authResponse = $this->requireAuth();
     *     if ($authResponse) {
     *         return $authResponse;
     *     }
     * }
     *
     * @return \Cake\Http\Response|null Error response 401 or null if OK
     */
    protected function requireAuth()
    {
        $decoded = $this->verifyJwtToken();

        // If token is not valid
        if (!$decoded) {
            // Return HTTP 401 Unauthorized error response
            return $this->response
                ->withType('application/json')
                ->withStatus(401)
                ->withStringBody(json_encode([
                    'success' => false,
                    'error' => 'Unauthenticated. Token missing or invalid.'
                ]));
        }

        // If valid, user is authenticated, all is OK
        $this->currentUser = $decoded;
        return null;
    }


    /**
     * Get the complete User entity from the database
     *
     * Uses the ID stored in the JWT ('sub' field) to load
     * the complete user with all their fields.
     *
     * @return \App\Model\Entity\User|null The User entity or null
     */
    protected function getCurrentUser()
    {
        if (!$this->currentUser) {
            return null;
        }
        $usersTable = $this->fetchTable('Users');

        // Load the user from the database using the ID from the JWT
        // equivalent to: $usersTable->get($user->id)
        return $usersTable->get($this->currentUser->sub);
    }
}
