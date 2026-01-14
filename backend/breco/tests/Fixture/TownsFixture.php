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
                'zipcode' => '',
                'name' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-14 13:11:24',
                'modified' => '2026-01-14 13:11:24',
            ],
        ];
        parent::init();
    }
}
