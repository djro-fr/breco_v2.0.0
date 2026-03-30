<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddLockedAtToUsers extends BaseMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table
            ->addColumn('locked_at', 'datetime', ['null' => true, 'default' => null])
            ->update();
    }
}
