<?php
declare(strict_types=1);

use Migrations\BaseMigration;
use Migrations\Db\Action\AddColumn;

class CreateTripRequests extends BaseMigration
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
        $table = $this->table('trip_requests');
        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'comment' => 'Passager demandant un trajet (FK users)'
        ])
        ->addColumn('departure_location_id', 'integer', [
            'null' => false,
            'comment' => 'Lieu de départ souhaité (FK locations)'
        ])
        ->addColumn('arrival_location_id', 'integer', [
            'null' => false,
            'comment' => 'Lieu d\'arrivée souhaité (FK locations)'
        ])
        ->addColumn('departure_time', 'datetime', [
            'null' => false
        ])
        ->addColumn('arrival_time', 'datetime', [
            'null' => false
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE'
            ])
        ->addForeignKey('departure_location_id', 'locations', 'id', [
            'delete' => 'RESTRICT',
            'update' => 'CASCADE'
        ])
        ->addForeignKey('arrival_location_id', 'locations', 'id', [
            'delete' => 'RESTRICT',
            'update' => 'CASCADE'
        ])
        ->addIndex(['user_id'])
        ->addIndex(['departure_location_id'])
        ->addIndex(['arrival_location_id'])
        ->addIndex(['departure_time'])
        ;
        $table->create();
    }
}
