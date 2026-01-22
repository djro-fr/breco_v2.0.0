<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ReplaceCarPoolingAreaWithType extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('locations');

        $table->addColumn('type', 'string', [
            'limit' => 50,
            'null' => false,
            'default' => 'parking',
            'after' => 'gps_lng',
            'comment' => 'Type de lieu (Aire de covoiturage, Parking, Supermarché, etc.)'
        ])
        ->update();

        $this->execute("UPDATE locations SET type = CASE WHEN carpooling_area = 1 THEN 'Aire de covoiturage' ELSE 'Parking' END");

        $table->removeColumn('carpooling_area');
        $table->addIndex(['type']);
        $table->update();
    }
}
