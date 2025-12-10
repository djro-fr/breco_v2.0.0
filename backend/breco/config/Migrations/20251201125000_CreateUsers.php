<?php
// backend\breco\config\Migrations\20251109102601_CreateUsers.php

declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUsers extends BaseMigration
{
    public function up(): void
    {
        $table = $this->table('users');
        $table->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('driver', 'boolean', ['default' => false, 'null' => false])
            ->addColumn('gender', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('zip_code', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('town', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('car_model', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('car_color', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('car_seat_nb', 'integer', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => false])
            ->addIndex('email', ['unique' => true])
            ->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->save();
    }
}
