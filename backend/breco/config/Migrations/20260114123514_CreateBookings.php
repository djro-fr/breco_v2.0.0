<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateBookings extends BaseMigration
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
        $table = $this->table('bookings', [
            'comment' => 'Réservations de passagers sur des trajets'
        ]);
        $table
        ->addColumn('trip_id', 'integer', [
            'null' => false,
            'comment' => 'Trajet réservé (FK trips)'
        ])
        ->addColumn('user_id', 'integer', [
            'null' => false,
            'comment' => 'Passager (FK users)'
        ])
        ->addColumn('trip_request_id', 'integer', [
            'null' => true,
            'comment' => 'Demande de trajet associé (FK trip_requests)'
        ])
        ->addColumn('seats_reserved', 'integer', [
            'null' => false,
            'default' => 1,
            'comment' => 'Nombre de places réservées'
        ])
        ->addColumn('status', 'string', [
            'limit' => 45,
            'null' => false
        ])
        ->addColumn('created', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => true
        ])
        ->addForeignKey('trip_id', 'trips', 'id', [
            'delete' => 'CASCADE', // if trip is deleted, delete bookings too
            'update' => 'CASCADE'
        ])
        ->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'RESTRICT', // do not allow deleting user if they have bookings
            'update' => 'CASCADE'
            ])
        ->addForeignKey('trip_request_id', 'trip_requests', 'id', [
            'delete' => 'SET_NULL', // if trip request is deleted, set to null
            'update' => 'CASCADE'
        ])
        ->addIndex(['trip_id'])
        ->addIndex(['user_id'])
        ->addIndex(['trip_request_id'])
        ->addIndex(['status'])
        ->addIndex(['trip_id', 'user_id'], [
            'unique' => true,
            'name' => 'unique_booking_per_user_trip'
        ])
        ;
        $table->create();
    }
}
