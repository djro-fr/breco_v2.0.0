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
                'email' => 'test@breco.fr',
                'email_verified' => 1,
                'verification_token' => 'abc123token',
                'verification_token_expires' => '2026-12-31 23:59:59',
                'password' => '$2Y10abc',
                'last_name' => 'Dupont',
                'first_name' => 'Jean',
                'phone' => '0612345678',
                'age' => 30,
                'gender' => 'Homme',
                'created' => '2026-01-14 13:11:39',
                'modified' => '2026-01-14 13:11:39',
            ],
        ];
        parent::init();
    }
}
