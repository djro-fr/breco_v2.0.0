<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateDrivers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('drivers');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'comment' => 'Référence vers l\'utilisateur (FK)'
        ])
        ->addColumn('car_model', 'string', [
            'limit' => 100,
            'null' => false,
            'comment' => 'Modèle de la voiture'
        ])
        ->addColumn('car_color', 'string', [
            'limit' => 45,
            'null' => false,
            'comment' => 'Couleur de la voiture'
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addIndex(['user_id'], ['unique' => true])
        ->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE', // if user is deleted, delete driver too
            'update' => 'CASCADE'
        ])
        ;
        $table->create();
    }
}
