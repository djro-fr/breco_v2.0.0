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
                'name' => 'Rennes',
                'postal_code' => '35000',
                'insee_code' => '35238',
                'created' => '2026-01-14 13:11:39',
                'modified' => '2026-01-14 13:11:39',
            ],
        ];
        parent::init();
    }
}
