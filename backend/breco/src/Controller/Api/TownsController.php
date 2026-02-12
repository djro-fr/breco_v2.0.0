<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use App\Service\Town\TownSearchService;
use App\Dto\Town\TownSearchRequest;
use Cake\Http\Response;

class TownsController extends AppController
{
    private TownSearchService $townSearchService;

    public function initialize(): void
    {
        parent::initialize();
        $this->townSearchService = new TownSearchService();
    }

    /**
     * Helper to return JSON with UTF-8 support
     */
    private function jsonResponse(array $data, int $status = 200): Response
    {
        return $this->response
            ->withType('application/json')
            ->withCharset('UTF-8')
            ->withStatus($status)
            ->withStringBody(
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
    }

    /**
     * GET /api/towns/search?q=...&limit=...
     *
     * Search for towns by name with optional limit (default 10)
     * Returns JSON with matching towns and total count     *
     */
    public function search()
    {
        $this->request->allowMethod(['get']);

        $query = $this->request->getQuery('q');
        $limit = (int)($this->request->getQuery('limit') ?? 10);

        try {
            $request = new TownSearchRequest($query, $limit);
            $result = $this->townSearchService->search($request);

            return $this->jsonResponse([
                'success' => true,
                'data' => $result['towns'],
                'count' => $result['count'],
                'query' => $result['query']
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse([
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');
            return $this->jsonResponse([
                'error' => 'Server error'
            ], 500);
        }
    }

    /**
     * GET /api/towns?limit=...&offset=...
     *
     * List all towns with pagination
     * Returns JSON with towns, total count, limit and offset
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        $limit = (int)($this->request->getQuery('limit') ?? 50);
        $offset = (int)($this->request->getQuery('offset') ?? 0);

        try {
            $result = $this->townSearchService->listAll($limit, $offset);

            return $this->jsonResponse([
                'success' => true,
                'data' => $result['towns'],
                'count' => $result['count'],
                'limit' => $limit,
                'offset' => $offset
            ]);

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');
            return $this->jsonResponse([
                'error' => 'Server error'
            ], 500);
        }
    }

    /**
     * GET /api/towns/{id}
     *
     * Get town details by ID
     * Returns JSON with town data or error if not found
     */
    public function view($id = null)
    {
        $this->request->allowMethod(['get']);

        try {
            if (!$id) {
                throw new \InvalidArgumentException('ID required');
            }

            $town = $this->townSearchService->getById((int)$id);

            if (!$town) {
                return $this->jsonResponse([
                    'error' => 'Town not found'
                ], 404);
            }

            return $this->jsonResponse([
                'success' => true,
                'data' => $town
            ]);

        } catch (\InvalidArgumentException $e) {
            return $this->jsonResponse([
                'error' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');
            return $this->jsonResponse([
                'error' => 'Server error'
            ], 500);
        }
    }
}
