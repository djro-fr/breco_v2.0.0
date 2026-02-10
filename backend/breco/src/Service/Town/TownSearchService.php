<?php
declare(strict_types=1);

namespace App\Service\Town;

use App\Repository\TownRepository;
use App\Dto\Town\TownSearchRequest;

/**
 * Service class for handling town-related operations
 * (Business logic for searching towns, used by TownsController)
 *
 */
class TownSearchService
{
    private TownRepository $townRepository;

    public function __construct(?TownRepository $townRepository = null)
    {
        // check if repository is injected (for testing), otherwise create a new instance
        $this->townRepository = $townRepository ?? new TownRepository();
    }

    /**
     * List all towns (with optional pagination)
     *
     * @param int|null $limit Maximum number of results
     * @param int $offset Starting offset
     * @return array
     */
    public function listAll(?int $limit = null, int $offset = 0): array
    {
        $results = $this->townRepository->findAll($limit, $offset);
        return $this->formatResults($results);
    }

    /**
     * Search towns by criteria
     *
     * @param TownSearchRequest $request
     * @return array
     */
    public function search(TownSearchRequest $request): array
    {
        $query = $request->getQuery();
        $limit = $request->getLimit();

        // Search by name
        $results = $this->townRepository->searchByName($query, $limit);

        // If no results and query looks like a postal code, try that
        if (empty($results) && $this->isPostalCode($query)) {
            $results = $this->townRepository->findByPostalCode($query);
        }

        return $this->formatResults($results);
    }

    /**
     * Get a single town by ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $town = $this->townRepository->findById($id);

        if (!$town) {
            return null;
        }

        return $this->formatResult($town);
    }

    /**
     * Check if string looks like a French postal code
     *
     * @param string $query
     * @return bool
     */
    private function isPostalCode(string $query): bool
    {
        return preg_match('/^\d{5}$/', $query) === 1;
    }

    /**
     * Format a single result for API response
     *
     * @param array $town
     * @return array
     */
    private function formatResult(array $town): array
    {
        return [
            'id' => (int)$town['id'],
            'name' => $town['name'],
            'postal_code' => $town['postal_code'],
            'insee_code' => $town['insee_code'] ?? null,
        ];
    }

    /**
     * Format multiple results for API response
     *
     * @param array $results
     * @return array
     */
    private function formatResults(array $results): array
    {
        return array_map([$this, 'formatResult'], $results);
    }
}
