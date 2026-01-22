<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LocationsFixture
 */
class LocationsFixture extends TestFixture
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
                'town_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'address' => 'Lorem ipsum dolor sit amet',
                'gps_lat' => 1.5,
                'gps_lng' => 1.5,
                'type' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-22 10:15:23',
                'modified' => '2026-01-22 10:15:23',
            ],
        ];
        parent::init();
    }
}
