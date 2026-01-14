<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateTowns extends BaseMigration
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
        $table = $this->table('towns');
        $table->addColumn('zipcode', 'char', [
            'limit' => 5,
            'null' => false,
            'comment' => 'Code postal de la ville'
        ])
        ->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false,
            'comment' => 'Nom de la ville'
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addIndex(['zipcode'])
        ->addIndex(['name'])
        ->addIndex(['zipcode', 'name'], ['unique' => true])
        ->create();
    }
}
