<?php
declare(strict_types=1);

namespace App\Repository;

use Cake\ORM\Locator\LocatorAwareTrait;

class LocationRepository
{
    use LocatorAwareTrait;

    private $table;

    public function __construct()
    {
        $this->table = $this->fetchTable('Locations');
    }

    /**
     * Find all locations with towns
     *
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $query = $this->table->find()
            ->select(['id', 'name', 'address', 'gps_lat', 'gps_lng', 'type', 'town_id'])
            ->contain(['Towns' => function ($q) {
                return $q->select(['id', 'name', 'postal_code', 'insee_code']);
            }])
            ->orderBy(['Towns.name' => 'ASC', 'Locations.name' => 'ASC'])
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
     * Search locations by town name or postal code
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchByTown(string $query, int $limit = 10): array
    {
        return $this->table->find()
            ->select(['id', 'name', 'address', 'gps_lat', 'gps_lng', 'type', 'town_id'])
            ->contain(['Towns' => function ($q) {
                return $q->select(['id', 'name', 'postal_code', 'insee_code']);
            }])
            ->where([
                'OR' => [
                    'Towns.name LIKE' => $query . '%',
                    'Towns.postal_code LIKE' => $query . '%'
                ]
            ])
            ->orderBy(['Towns.name' => 'ASC', 'Locations.name' => 'ASC'])
            ->limit($limit)
            ->enableHydration(false)
            ->toArray();
    }

    /**
     * Find location by ID with town
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        return $this->table->find()
            ->select(['id', 'name', 'address', 'gps_lat', 'gps_lng', 'type', 'town_id'])
            ->contain(['Towns' => function ($q) {
                return $q->select(['id', 'name', 'postal_code', 'insee_code']);
            }])
            ->where(['Locations.id' => $id])
            ->enableHydration(false)
            ->first();
    }

    /**
     * Find locations by town ID
     *
     * @param int $townId
     * @return array
     */
    public function findByTownId(int $townId): array
    {
        return $this->table->find()
            ->select(['id', 'name', 'address', 'gps_lat', 'gps_lng', 'type', 'town_id'])
            ->contain(['Towns' => function ($q) {
                return $q->select(['id', 'name', 'postal_code', 'insee_code']);
            }])
            ->where(['Locations.town_id' => $townId])
            ->orderBy(['Locations.name' => 'ASC'])
            ->enableHydration(false)
            ->toArray();
    }

    /**
     * Find locations by type
     *
     * @param string $type
     * @return array
     */
    public function findByType(string $type): array
    {
        return $this->table->find()
            ->select(['id', 'name', 'address', 'gps_lat', 'gps_lng', 'type', 'town_id'])
            ->contain(['Towns' => function ($q) {
                return $q->select(['id', 'name', 'postal_code', 'insee_code']);
            }])
            ->where(['Locations.type' => $type])
            ->orderBy(['Towns.name' => 'ASC', 'Locations.name' => 'ASC'])
            ->enableHydration(false)
            ->toArray();
    }
}
