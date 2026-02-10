<?php
declare(strict_types=1);

namespace App\Dto\Location;

class LocationSearchRequest
{
    private string $query;
    private int $limit;

    public function __construct(string $query, int $limit = 10)
    {
        $this->validate($query, $limit);

        $this->query = trim($query);
        $this->limit = $limit;
    }

    private function validate(string $query, int $limit): void
    {
        if (strlen($query) < 2) {
            throw new \InvalidArgumentException(
                'Search query must be at least 2 characters'
            );
        }

        if ($limit < 1 || $limit > 50) {
            throw new \InvalidArgumentException(
                'Limit must be between 1 and 50'
            );
        }
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
