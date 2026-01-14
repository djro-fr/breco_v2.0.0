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
                'carpooling_area' => 1,
                'created' => '2026-01-14 13:11:52',
                'modified' => '2026-01-14 13:11:52',
            ],
        ];
        parent::init();
    }
}
