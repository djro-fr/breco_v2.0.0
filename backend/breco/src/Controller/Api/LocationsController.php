<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\Location\LocationSearchService;
use App\Dto\Location\LocationSearchRequest;
use Cake\Http\Response;
use SwaggerBake\Lib\Attribute as Swag;

/**
 * Locations Controller
 *
 * Handles carpooling location operations
 */
class LocationsController extends AppController
{
    private LocationSearchService $locationSearchService;

    public function initialize(): void
    {
        parent::initialize();
        $this->locationSearchService = new LocationSearchService();
    }

    /**
     * List all locations
     * GET /api/locations?limit=50&offset=0
     */
    public function index(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $limit = $this->request->getQuery('limit');
            $offset = (int) $this->request->getQuery('offset', 0);

            if ($limit !== null) {
                $limit = (int) $limit;
            }

            $locations = $this->locationSearchService->listAll($limit, $offset);

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $locations,
                    'count' => count($locations)
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

    /**
     * Search locations by town name or postal code
     * GET /api/locations/search?q=Quimper&limit=10
     */
    #[Swag\OpenApiQueryParam(name: 'q', type: 'string', description: 'Search term (town name or postal code)', example: 'quimper', isRequired: false)]
    #[Swag\OpenApiQueryParam(name: 'limit', type: 'integer', description: 'Max results (default 10)', example: '10', isRequired: false)]
    public function search(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $query = $this->request->getQuery('q', '');
            $limit = (int) $this->request->getQuery('limit', 10);

            $searchRequest = new LocationSearchRequest($query, $limit);
            $results = $this->locationSearchService->search($searchRequest);

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $results,
                    'count' => count($results),
                    'query' => $query
                ]));
        } catch (\InvalidArgumentException $e) {
            return $this->response
                ->withType('application/json')
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
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

    /**
     * Get locations by town ID
     * GET /api/locations/by-town/{town_id}
     */
    public function byTown($townId = null): Response
    {
        $this->request->allowMethod(['get']);

        try {
            if ($townId === null) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Town ID is required'
                    ]));
            }

            $locations = $this->locationSearchService->getByTownId((int)$townId);

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $locations,
                    'count' => count($locations),
                    'town_id' => (int)$townId
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

    /**
     * Get locations by type
     * GET /api/locations/by-type/{type}
     */
    #[Swag\OpenApiPathParam(name: 'type', type: 'string', description: 'Location type: Aire de covoiturage, Parking, Supermarché', example: 'Parking')]
    public function byType($type = null): Response
    {
        $this->request->allowMethod(['get']);

        try {
            if ($type === null) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Location type is required'
                    ]));
            }

            $locations = $this->locationSearchService->getByType($type);

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $locations,
                    'count' => count($locations),
                    'type' => $type
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

    /**
     * View a single location
     * GET /api/locations/{id}
     */
    public function view($id = null): Response
    {
        $this->request->allowMethod(['get']);

        try {
            if ($id === null) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Location ID is required'
                    ]));
            }

            $location = $this->locationSearchService->getById((int)$id);

            if (!$location) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(404)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Location not found'
                    ]));
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $location
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
