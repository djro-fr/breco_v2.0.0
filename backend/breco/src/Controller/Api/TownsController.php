<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\Town\TownSearchService;
use App\Dto\Town\TownSearchRequest;
use Cake\Http\Response;

/**
 * Towns Controller
 * (HTTP API controller for managing towns,
 * uses TownSearchService for business logic)
 *
 * Handles town operations including listing
 * and searching Breton towns.
 */
class TownsController extends AppController
{
    private TownSearchService $townSearchService;

    public function initialize(): void
    {
        parent::initialize();

        // Inject the service
        $this->townSearchService = new TownSearchService();
    }

    /**
     * List all towns
     *
     * GET /api/towns
     * GET /api/towns?limit=50&offset=0
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $limit = $this->request->getQuery('limit');
            $offset = (int) $this->request->getQuery('offset', 0);

            // Convert limit to int if provided
            if ($limit !== null) {
                $limit = (int) $limit;
            }

            $towns = $this->townSearchService->listAll($limit, $offset);

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $towns,
                    'count' => count($towns)
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
     * Search towns by name or postal code
     *
     * GET /api/towns/search?q=Rennes&limit=10
     *
     * @return Response
     */
    public function search(): Response
    {
        $this->request->allowMethod(['get']);

        try {
            $query = $this->request->getQuery('q', '');
            $limit = (int) $this->request->getQuery('limit', 10);

            // Create DTO (automatic validation)
            $searchRequest = new TownSearchRequest($query, $limit);

            // Execute service
            $results = $this->townSearchService->search($searchRequest);

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
     * View a single town
     *
     * GET /api/towns/:id
     *
     * @param int|null $id Town ID
     * @return Response
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
                        'message' => 'Town ID is required'
                    ]));
            }

            $town = $this->townSearchService->getById((int)$id);

            if (!$town) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(404)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Town not found'
                    ]));
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $town
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
