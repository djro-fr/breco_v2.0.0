<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddEmailVerificationToUsers extends BaseMigration
{
    /**
     * Add email verification columns to users table
     */
    public function change(): void
    {
        $table = $this->table('users');

        $table
            ->addColumn('email_verified', 'boolean', [
                'default' => false,
                'null' => false,
                'after' => 'email',
                'comment' => 'Email vérifié ou non'
            ])
            ->addColumn('verification_token', 'string', [
                'limit' => 255,
                'null' => true,
                'after' => 'email_verified',
                'comment' => 'Token de vérification email'
            ])
            ->addColumn('verification_token_expires', 'datetime', [
                'null' => true,
                'after' => 'verification_token',
                'comment' => 'Date d\'expiration du token'
            ])
            ->addIndex('verification_token', [
                'name' => 'idx_verification_token'
            ])
            ->update();
    }
}
