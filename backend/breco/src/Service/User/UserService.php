<?php
declare(strict_types=1);

namespace App\Service\User;

use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(?UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    /**
     * List all users
     *
     * @return array
     */
    public function listAll(): array
    {
        $users = $this->userRepository->findAll();
        return $this->formatResults($users);
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return null;
        }

        return $this->formatUser($user);
    }

    /**
     * Format a single user
     *
     * @param array $user
     * @return array
     */
    private function formatUser(array $user): array
    {
        return [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'lastName' => $user['last_name'],
            'firstName' => $user['first_name'],
            'phone' => $user['phone'],
            'age' => $user['age'] ?? null,
            'gender' => $user['gender'] ?? null,
            'townId' => $user['town_id'] ?? null,
            'created' => isset($user['created']) ? $user['created']->format('Y-m-d H:i:s') : null,
            'modified' => isset($user['modified']) ? $user['modified']->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Format multiple users
     *
     * @param array $users
     * @return array
     */
    private function formatResults(array $users): array
    {
        return array_map([$this, 'formatUser'], $users);
    }
}
