<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateLocations extends BaseMigration
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
        $table = $this->table('locations');

        $table->addColumn('town_id', 'integer', [
            'null' => false,
            'comment' => 'Référence vers la ville (FK)'
        ])
        ->addColumn('name', 'string', [
            'limit' => 45,
            'null' => false,
            'comment' => 'Nom du lieu'
        ])
        ->addColumn('address', 'string', [
            'limit' => 255,
            'null' => false,
        ])
        ->addColumn('gps_lat', 'decimal', [
            'precision' => 10,  // Total number of digits
            'scale' => 8,       // Number of decimal places
            'comment' => 'Latitude GPS'
        ])
        ->addColumn('gps_lng', 'decimal', [
            'precision' => 11,  // Total number of digits (up to 180 degrees)
            'scale' => 8,       // Number of decimal places
            'comment' => 'Longitude GPS'
        ])
        ->addColumn('carpooling_area', 'boolean', [
            'default' => false,
            'comment' => 'vrai si zone de covoiturage officielle'
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
            'delete' => 'RESTRICT',  // Prevents deletion of a town if locations reference it
            'update' => 'CASCADE'    // Automatically update town_id on locations if the referenced town's id changes
        ])
        ->addIndex(['town_id'])
        ->addIndex(['name'])
        ->create();
    }
}
