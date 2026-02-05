<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

/**
 * Locations Controller
 *
 * Handles carpooling location operations including listing,
 * searching, and viewing carpooling spots in Brittany.
 */
class LocationsController extends AppController
{

    /**
     * List all locations
     *
     * GET /api/locations
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $this->request->allowMethod(['get']);

        try {
            $locationsTable = $this->fetchTable('Locations');

            $locations = $locationsTable->find()
                ->contain(['Towns'])
                ->orderBy(['Towns.name' => 'ASC', 'Locations.name' => 'ASC'])
                ->all();

            $result = [];
            foreach ($locations as $location) {
                $result[] = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'gps_lat' => $location->gps_lat,
                    'gps_lng' => $location->gps_lng,
                    'type' => $location->type,
                    'town' => [
                        'id' => $location->town->id,
                        'name' => $location->town->name,
                        'postal_code' => $location->town->postal_code,
                        'insee_code' => $location->town->insee_code
                    ]
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
     * Search locations by name
     *
     * GET /api/locations/search?q=Parking
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

            $locationsTable = $this->fetchTable('Locations');

            $locations = $locationsTable->find()
                ->contain(['Towns'])
                ->where(['Locations.name LIKE' => '%' . $query . '%'])
                ->orderBy(['Locations.name' => 'ASC'])
                ->limit(50)
                ->all();

            $result = [];
            foreach ($locations as $location) {
                $result[] = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'gps_lat' => $location->gps_lat,
                    'gps_lng' => $location->gps_lng,
                    'type' => $location->type,
                    'town' => [
                        'id' => $location->town->id,
                        'name' => $location->town->name,
                        'postal_code' => $location->town->postal_code,
                        'insee_code' => $location->town->insee_code
                    ]
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
     * Get locations by town
     *
     * GET /api/locations/by-town/:town_id
     *
     * @param int $townId Town ID
     * @return \Cake\Http\Response
     */
    public function byTown($townId = null)
    {
        $this->request->allowMethod(['get']);

        try {
            if (empty($townId)) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'ID de ville requis'
                    ]));
            }

            $locationsTable = $this->fetchTable('Locations');

            $locations = $locationsTable->find()
                ->contain(['Towns'])
                ->where(['Locations.town_id' => $townId])
                ->orderBy(['Locations.name' => 'ASC'])
                ->all();

            $result = [];
            foreach ($locations as $location) {
                $result[] = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'gps_lat' => $location->gps_lat,
                    'gps_lng' => $location->gps_lng,
                    'type' => $location->type,
                    'town' => [
                        'id' => $location->town->id,
                        'name' => $location->town->name,
                        'postal_code' => $location->town->postal_code,
                        'insee_code' => $location->town->insee_code
                    ]
                ];
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $result,
                    'count' => count($result),
                    'town_id' => $townId
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
     * View a single location
     *
     * GET /api/locations/:id
     *
     * @param int $id Location ID
     * @return \Cake\Http\Response
     */
    public function view($id = null)
    {
        $this->request->allowMethod(['get']);

        try {
            $locationsTable = $this->fetchTable('Locations');
            $location = $locationsTable->find()
            ->where(['Locations.id' => $id])
            ->contain(['Towns'])
            ->first();

            if (!$location) {
            return $this->response
                ->withType('application/json')
                ->withStatus(404)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Lieu de covoiturage non trouvé'
                ]));
        }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $location->id,
                        'name' => $location->name,
                        'address' => $location->address,
                        'gps_lat' => $location->gps_lat,
                        'gps_lng' => $location->gps_lng,
                        'type' => $location->type,
                        'town' => [
                            'id' => $location->town->id,
                            'name' => $location->town->name,
                            'postal_code' => $location->town->postal_code,
                            'insee_code' => $location->town->insee_code
                        ]
                    ]
                ]));
        } catch (NotFoundException $e) {
            return $this->response
                ->withType('application/json')
                ->withStatus(404)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Lieu de covoiturage non trouvé'
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
     * Get locations by type
     *
     * GET /api/locations/by-type/:type
     *
     * @param string $type Location type
     * @return \Cake\Http\Response
     */
    public function byType($type = null)
    {
        $this->request->allowMethod(['get']);

        try {
            if (empty($type)) {
                return $this->response
                    ->withType('application/json')
                    ->withStatus(400)
                    ->withStringBody(json_encode([
                        'success' => false,
                        'message' => 'Type de lieu requis'
                    ]));
            }

            $locationsTable = $this->fetchTable('Locations');

            $locations = $locationsTable->find()
                ->contain(['Towns'])
                ->where(['Locations.type' => $type])
                ->orderBy(['Towns.name' => 'ASC', 'Locations.name' => 'ASC'])
                ->all();

            $result = [];
            foreach ($locations as $location) {
                $result[] = [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address,
                    'gps_lat' => $location->gps_lat,
                    'gps_lng' => $location->gps_lng,
                    'type' => $location->type,
                    'town' => [
                        'id' => $location->town->id,
                        'name' => $location->town->name,
                        'postal_code' => $location->town->postal_code,
                        'insee_code' => $location->town->insee_code
                    ]
                ];
            }

            return $this->response
                ->withType('application/json')
                ->withStatus(200)
                ->withStringBody(json_encode([
                    'success' => true,
                    'data' => $result,
                    'count' => count($result),
                    'type' => $type
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
