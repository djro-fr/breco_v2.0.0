<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TripRequestsFixture
 */
class TripRequestsFixture extends TestFixture
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
                'user_id' => 1,
                'departure_location_id' => 1,
                'arrival_location_id' => 1,
                'departure_time' => '2026-01-14 13:12:02',
                'arrival_time' => '2026-01-14 13:12:02',
                'created' => '2026-01-14 13:12:02',
                'modified' => '2026-01-14 13:12:02',
            ],
        ];
        parent::init();
    }
}
