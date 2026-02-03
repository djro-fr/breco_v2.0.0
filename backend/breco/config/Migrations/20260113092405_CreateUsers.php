<?php
// backend\breco\config\Migrations\20251109102601_CreateUsers.php

declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUsers extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('town_id', 'integer', [
        'null' => true,
        'comment' => 'Référence vers la ville'
        ])
            ->addColumn('email', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('age', 'integer', [ 'default' => 0, 'null' => true])
            ->addColumn('gender', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => true
            ])
            ->addIndex('email', ['unique' => true])
            ->addIndex(['town_id'])
            ->addForeignKey('town_id', 'towns', 'id', [
                'delete' => 'RESTRICT',  // Prevents deletion of a town if locations reference it
                'update' => 'CASCADE'    // Automatically update town_id on locations if the referenced town's id changes
            ])
            ->create();
    }

}
