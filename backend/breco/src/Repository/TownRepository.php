<?php
declare(strict_types=1);

namespace App\Repository;

use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Repository class for accessing town data
 * (Data access layer for Towns, used by TownSearchService)
 *
 * Executes SQL queries to retrieve town information from the database
 *
 */
class TownRepository
{
    use LocatorAwareTrait;

    private $table;

    public function __construct()
    {
        $this->table = $this->fetchTable('Towns');
    }

    /**
     * Find all towns (with optional pagination)
     *
     * @param int|null $limit Maximum number of results
     * @param int $offset Starting offset
     * @return array
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $query = $this->table->find()
            ->select(['id', 'name', 'postal_code', 'insee_code'])
            ->orderBy(['name' => 'ASC'])
            ->enableHydration(false);

        if ($offset > 0) {
            $query->offset($offset);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->toArray();
    }

    /**
     * Search towns by name
     *
     * @param string $query Search term
     * @param int $limit Maximum number of results
     * @return array
     */
    public function searchByName(string $query, int $limit = 10): array
    {
        return $this->table->find()
            ->select(['id', 'name', 'postal_code', 'insee_code'])
            ->where(['name LIKE' => $query . '%'])
            ->orderBy(['name' => 'ASC'])
            ->limit($limit)
            ->enableHydration(false)
            ->toArray();
    }

    /**
     * Find a town by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        return $this->table->find()
            ->select(['id', 'name', 'postal_code', 'insee_code'])
            ->where(['id' => $id])
            ->enableHydration(false)
            ->first();
    }

    /**
     * Find towns by postal code
     *
     * @param string $postalCode
     * @return array
     */
    public function findByPostalCode(string $postalCode): array
    {
        return $this->table->find()
            ->select(['id', 'name', 'postal_code', 'insee_code'])
            ->where(['postal_code' => $postalCode])
            ->enableHydration(false)
            ->toArray();
    }
}
