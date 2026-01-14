<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
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
                'email' => 'Lorem ipsum dolor sit amet',
                'email_verified' => 1,
                'verification_token' => 'Lorem ipsum dolor sit amet',
                'verification_token_expires' => '2026-01-14 13:11:39',
                'password' => 'Lorem ipsum dolor ',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'first_name' => 'Lorem ipsum dolor sit amet',
                'phone' => 'Lorem ipsum dolor ',
                'age' => 1,
                'gender' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-14 13:11:39',
                'modified' => '2026-01-14 13:11:39',
            ],
        ];
        parent::init();
    }
}
