<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class ReplaceZipcodeWithPostalCodeAndInseeCodeInTowns extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('towns');

        $table->addColumn('postal_code', 'char', [
            'limit' => 5,
            'null' => false,
            'default' => '',
            'after' => 'name',
            'comment' => 'Code postal'
        ])
        ->addColumn('insee_code', 'char', [
            'limit' => 5,
            'null' => false,
            'default' => '',
            'after' => 'postal_code',
            'comment' => 'Code INSEE de la commune'
        ])
        ->update();

        $this->execute('UPDATE towns SET postal_code = zipcode WHERE zipcode IS NOT NULL AND zipcode != ""');

        $table->removeIndexByName('zipcode');
        $table->removeColumn('zipcode');

        $table->addIndex(['postal_code'], ['name' => 'postal_code'])
              ->addIndex(['insee_code'], ['name' => 'insee_code'])
              ->addIndex(['postal_code', 'name'], ['unique' => true, 'name' => 'postal_code_name'])
              ->update();
    }
}
