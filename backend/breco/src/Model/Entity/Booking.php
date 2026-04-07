<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Booking Entity
 *
 * @property int $id
 * @property int $trip_id
 * @property int $user_id
 * @property int|null $trip_request_id
 * @property int $seats_reserved
 * @property string $status
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Trip $trip
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\TripRequest $trip_request
 */
class Booking extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $accessible = [
        'trip_id' => true,
        'user_id' => true,
        'trip_request_id' => true,
        'seats_reserved' => true,
        'status' => true,
        'created' => true,
        'modified' => true,
        'trip' => true,
        'user' => true,
        'trip_request' => true,
    ];
}
