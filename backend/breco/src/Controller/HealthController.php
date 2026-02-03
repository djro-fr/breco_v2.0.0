<?php
declare(strict_types=1);

namespace App\Controller;

class HealthController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    // GET /api/health - Health check endpoint
    public function index()
    {
        $this->autoRender = false;

        $response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'status' => 'ok',
                'service' => 'breco-backend',
                'timestamp' => date('Y-m-d H:i:s')
            ]));

        return $response;
    }
}
