<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

/**
 * Towns Controller
 *
 * Handles town operations including listing and searching
 * Breton towns.
 */
class TownsController extends AppController
{
    /**
     * Initialization hook method
     */
    public function initialize(): void
    {
        parent::initialize();

        // Allow public access to towns data
        $this->Authentication->addUnauthenticatedActions(['index', 'view', 'search']);
    }

    /**
     * List all towns
     *
     * GET /api/towns
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        try {
            $townsTable = $this->fetchTable('Towns');

            $towns = $townsTable->find()
                ->orderBy(['Towns.name' => 'ASC'])
                ->all();

            $result = [];
            foreach ($towns as $town) {
                $result[] = [
                    'id' => $town->id,
                    'name' => $town->name,
                    'postal_code' => $town->postal_code,
                    'insee_code' => $town->insee_code
                ];
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $result,
                    'count' => count($result)
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withType('application/json')
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Erreur serveur'
                ]));
        }
    }

    /**
     * Search towns by name
     *
     * GET /api/towns/search?q=Rennes
     *
     * @return \Cake\Http\Response
     */
    public function search()
    {
        $this->request->allowMethod(['get']);

        try {
            $query = $this->request->getQuery('q');

            if (empty($query)) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Paramètre de recherche requis'
                    ]));
            }

            $townsTable = $this->fetchTable('Towns');

            $towns = $townsTable->find()
                ->where(['Towns.name LIKE' => '%' . $query . '%'])
                ->orderBy(['Towns.name' => 'ASC'])
                ->limit(20)
                ->all();

            $result = [];
            foreach ($towns as $town) {
                $result[] = [
                    'id' => $town->id,
                    'name' => $town->name,
                    'postal_code' => $town->postal_code,
                    'insee_code' => $town->insee_code
                ];
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $result,
                    'count' => count($result),
                    'query' => $query
                ]));

        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withType('application/json')
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Erreur serveur'
                ]));
        }
    }

    /**
     * View a single town
     *
     * GET /api/towns/:id
     *
     * @param int $id Town ID
     * @return \Cake\Http\Response
     */
    public function view($id = null)
    {
        $this->request->allowMethod(['get']);

        try {
            $townsTable = $this->fetchTable('Towns');
            $town = $townsTable->find()
                ->where(['Towns.id' => $id])
                ->contain(['Locations'])
                ->first();
            if (!$town) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(404)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Ville non trouvée'
                    ]));
            }

            $locations = [];
            foreach ($town->locations as $location) {
                $locations[] = [
                    'id' => $location->id,
                    'name' => $location->name
                ];
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $town->id,
                        'name' => $town->name,
                        'postal_code' => $town->postal_code,
                        'insee_code' => $town->insee_code,
                        'locations' => $locations,
                        'locations_count' => count($locations)
                    ]
                ]));

        } catch (NotFoundException $e) {
            return $this->response
                ->withType('application/json')
                ->withStatus(404)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Ville non trouvée'
                ]));
        } catch (\Exception $e) {
            $this->log($e->getMessage(), 'error');

            return $this->response
                ->withType('application/json')
                ->withStatus(500)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Erreur serveur'
                ]));
        }
    }
}
