<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTrips extends BaseMigration
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
        $table = $this->table('trips', [
            'comment' => 'Trajets proposés par les conducteurs'
        ]);
        $table->addColumn('driver_id', 'integer', [
            'null' => false,
            'comment' => 'Référence vers le conducteur (FK drivers)'
        ])
        ->addColumn('departure_location_id', 'integer', [
            'null' => false,
            'comment' => 'Lieu de départ (FK locations)'
        ])
        ->addColumn('arrival_location_id', 'integer', [
            'null' => false,
            'comment' => 'Lieu d\'arrivée (FK locations)'
        ])
        ->addColumn('departure_time', 'datetime', [
            'null' => false,
            'comment' => 'Heure de départ'
        ])
        ->addColumn('arrival_time', 'datetime', [
            'null' => false,
            'comment' => 'Heure d\'arrivée'
        ])
        ->addColumn('available_seats', 'integer', [
            'null' => false,
            'default' => 3,
            'comment' => 'Nombre de places disponibles'
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addForeignKey('driver_id', 'drivers', 'id', [
            'delete' => 'RESTRICT',
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
        ->addIndex(['driver_id'])
        ->addIndex(['departure_location_id'])
        ->addIndex(['arrival_location_id'])
        ->addIndex(['departure_time'])
        ;
        $table->create();
    }
}
