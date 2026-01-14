<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TripsFixture
 */
class TripsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'driver_id' => 1,
                'departure_location_id' => 1,
                'arrival_location_id' => 1,
                'departure_time' => '2026-01-14 13:11:57',
                'arrival_time' => '2026-01-14 13:11:57',
                'available_seats' => 1,
                'created' => '2026-01-14 13:11:57',
                'modified' => '2026-01-14 13:11:57',
            ],
        ];
        parent::init();
    }
}
