<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateLocations extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('locations');
        $table->addColumn('town_id', 'integer', [
            'null' => false,
            'comment' => 'Référence vers la ville (FK)'
        ])
        ->addColumn('name', 'string', [
            'limit' => 255,
            'null' => false,
            'comment' => 'Nom du lieu'
        ])
        ->addColumn('address', 'string', [
            'limit' => 255,
            'null' => false,
        ])
        ->addColumn('gps_lat', 'decimal', [
            'precision' => 10,
            'scale' => 8,
            'comment' => 'Latitude GPS'
        ])
        ->addColumn('gps_lng', 'decimal', [
            'precision' => 11,
            'scale' => 8,
            'comment' => 'Longitude GPS'
        ])
        ->addColumn('type', 'string', [
            'limit' => 50,
            'null' => false,
            'default' => 'Parking',
            'comment' => 'Type de lieu (Aire de covoiturage, Parking, etc.)'
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addForeignKey('town_id', 'towns', 'id', [
            'delete' => 'RESTRICT',
            'update' => 'CASCADE'
        ])
        ->addIndex(['town_id'])
        ->addIndex(['name'])
        ->addIndex(['type'])
        ->create();
    }
}
