<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ReplaceZipcodeWithPostalCodeAndInseeCodeInLocations extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('locations');

        $table->addColumn('postal_code', 'char', [
            'limit' => 5,
            'null' => true,
            'default' => null,
            'after' => 'town_id',
            'comment' => 'Code postal'
        ])
        ->addColumn('insee_code', 'char', [
            'limit' => 5,
            'null' => true,
            'default' => null,
            'after' => 'postal_code',
            'comment' => 'Code INSEE de la commune'
        ])
        ->update();

        // Copy the zipcode values to postal_code (if zipcode exists)
        $columns = $table->getColumns();
        $hasZipcode = false;
        foreach ($columns as $column) {
            if ($column->getName() === 'zipcode') {
                $hasZipcode = true;
                break;
            }
        }

        if ($hasZipcode) {
            $this->execute('UPDATE locations SET postal_code = zipcode WHERE zipcode IS NOT NULL');

            $table->removeIndexByName('zipcode');
            $table->removeColumn('zipcode');
        }

        $table->addIndex(['postal_code'], ['name' => 'postal_code'])
              ->addIndex(['insee_code'], ['name' => 'insee_code'])
              ->update();
    }
}
