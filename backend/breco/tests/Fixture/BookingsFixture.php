<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BookingsFixture
 */
class BookingsFixture extends TestFixture
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
                'trip_id' => 1,
                'user_id' => 1,
                'trip_request_id' => 1,
                'seats_reserved' => 1,
                'status' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-14 13:12:09',
                'modified' => '2026-01-14 13:12:09',
            ],
        ];
        parent::init();
    }
}
