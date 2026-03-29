<?php
declare(strict_types=1);

namespace App\Repository;
use App\Exception\RepositoryException;

use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\I18n\DateTime;

class UserRepository
{
    use LocatorAwareTrait;

    private $table;

    public function __construct()
    {
        $this->table = $this->fetchTable('Users');
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->table->find()
            ->where(['email' => $email])
            ->enableHydration(false)
            ->first();
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        return $this->table->find()
            ->where(['id' => $id])
            ->enableHydration(false)
            ->first();
    }

    /**
     * Find user by verification token
     *
     * @param string $token
     * @return array|null
     */
    public function findByVerificationToken(string $token): ?array
    {
        return $this->table->find()
            ->where([
                'verification_token' => $token,
                'verification_token_expires >' => new DateTime()
            ])
            ->enableHydration(false)
            ->first();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return array User data with ID
     */
    public function create(array $data): array
    {
        $user = $this->table->newEntity($data);

        if (!$this->table->save($user)) {
            throw new RepositoryException('Failed to create user');
        }

        return $user->toArray();
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->table->get($id);
        $user = $this->table->patchEntity($user, $data);

        return $this->table->save($user) !== false;
    }

    /**
     * Verify user email
     *
     * @param int $id
     * @return bool
     */
    public function verifyEmail(int $id): bool
    {
        return $this->update($id, [
            'email_verified' => true,
            'verification_token' => null,
            'verification_token_expires' => null
        ]);
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return $this->table->exists(['email' => $email]);
    }

    /**
     * Set verification token
     *
     * @param int $id
     * @param string $token
     * @param DateTime $expiresAt
     * @return bool
     */
    public function setVerificationToken(int $id, string $token, DateTime $expiresAt): bool
    {
        return $this->update($id, [
            'verification_token' => $token,
            'verification_token_expires' => $expiresAt
        ]);
    }

    /**
     * Find all users
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->table->find()
            ->enableHydration(false)
            ->toArray();
    }

}
