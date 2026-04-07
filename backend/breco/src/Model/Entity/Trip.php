<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Trip Entity
 *
 * @property int $id
 * @property int $driver_id
 * @property int $departure_location_id
 * @property int $arrival_location_id
 * @property \Cake\I18n\DateTime $departure_time
 * @property \Cake\I18n\DateTime $arrival_time
 * @property int $available_seats
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Driver $driver
 * @property \App\Model\Entity\Location $departure_location
 * @property \App\Model\Entity\Location $arrival_location
 * @property \App\Model\Entity\Booking[] $bookings
 */
class Trip extends Entity
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
        'driver_id' => true,
        'departure_location_id' => true,
        'arrival_location_id' => true,
        'departure_time' => true,
        'arrival_time' => true,
        'available_seats' => true,
        'created' => true,
        'modified' => true,
        'driver' => true,
        'departure_location' => true,
        'arrival_location' => true,
        'bookings' => true,
    ];
}
