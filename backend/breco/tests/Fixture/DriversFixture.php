<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DriversFixture
 */
class DriversFixture extends TestFixture
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
                'car_model' => 'Lorem ipsum dolor sit amet',
                'car_color' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-14 13:11:46',
                'modified' => '2026-01-14 13:11:46',
            ],
        ];
        parent::init();
    }
}
