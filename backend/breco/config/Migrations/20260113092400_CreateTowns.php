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
        $table->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false,
            'comment' => 'Nom de la ville'
        ])
        ->addColumn('postal_code', 'char', [
            'limit' => 5,
            'null' => false,
            'default' => '',
            'comment' => 'Code postal'
        ])
        ->addColumn('insee_code', 'char', [
            'limit' => 5,
            'null' => false,
            'default' => '',
            'comment' => 'Code INSEE de la commune'
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true,
        ])
        ->addIndex(['name'])
        ->addIndex(['postal_code'])
        ->addIndex(['postal_code', 'name'])  // Composite index for faster searches
        ->addIndex(['insee_code'], ['unique' => true])
        ->create();
    }
}
