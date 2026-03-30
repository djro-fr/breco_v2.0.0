<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateLoginAttempts extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('login_attempts');
        $table
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('ip_address', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('attempted_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['email'])
            ->addIndex(['ip_address'])
            ->create();
    }
}
