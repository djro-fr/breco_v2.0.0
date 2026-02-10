<?php
declare(strict_types=1);

namespace App\Service\Location;

use App\Repository\LocationRepository;
use App\Dto\Location\LocationSearchRequest;

class LocationSearchService
{
    private LocationRepository $locationRepository;

    public function __construct(?LocationRepository $locationRepository = null)
    {
        $this->locationRepository = $locationRepository ?? new LocationRepository();
    }

    /**
     * List all locations
     *
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function listAll(?int $limit = null, int $offset = 0): array
    {
        $results = $this->locationRepository->findAll($limit, $offset);
        return $this->formatResults($results);
    }

    /**
     * Search locations by town name or postal code
     *
     * @param LocationSearchRequest $request
     * @return array
     */
    public function search(LocationSearchRequest $request): array
    {
        $query = $request->getQuery();
        $limit = $request->getLimit();

        $results = $this->locationRepository->searchByTown($query, $limit);

        return $this->formatResults($results);
    }

    /**
     * Get location by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $location = $this->locationRepository->findById($id);

        if (!$location) {
            return null;
        }

        return $this->formatResult($location);
    }

    /**
     * Get locations by town ID
     *
     * @param int $townId
     * @return array
     */
    public function getByTownId(int $townId): array
    {
        $results = $this->locationRepository->findByTownId($townId);
        return $this->formatResults($results);
    }

    /**
     * Get locations by type
     *
     * @param string $type
     * @return array
     */
    public function getByType(string $type): array
    {
        $results = $this->locationRepository->findByType($type);
        return $this->formatResults($results);
    }

    /**
     * Format a single result
     *
     * @param array $location
     * @return array
     */
    private function formatResult(array $location): array
    {
        return [
            'id' => (int)$location['id'],
            'name' => $location['name'],
            'address' => $location['address'],
            'gps_lat' => (float)$location['gps_lat'],
            'gps_lng' => (float)$location['gps_lng'],
            'type' => $location['type'],
            'town' => [
                'id' => (int)$location['town']['id'],
                'name' => $location['town']['name'],
                'postal_code' => $location['town']['postal_code'],
                'insee_code' => $location['town']['insee_code']
            ]
        ];
    }

    /**
     * Format multiple results
     *
     * @param array $results
     * @return array
     */
    private function formatResults(array $results): array
    {
        return array_map([$this, 'formatResult'], $results);
    }
}
