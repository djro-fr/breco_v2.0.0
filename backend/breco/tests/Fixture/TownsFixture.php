<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TownsFixture
 */
class TownsFixture extends TestFixture
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
                'name' => 'Lorem ipsum dolor sit amet',
                'postal_code' => '',
                'insee_code' => '',
                'created' => '2026-01-22 10:15:14',
                'modified' => '2026-01-22 10:15:14',
            ],
        ];
        parent::init();
    }
}
