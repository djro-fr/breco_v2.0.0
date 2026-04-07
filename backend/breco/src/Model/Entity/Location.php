<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Location Entity
 *
 * @property int $id
 * @property int $town_id
 * @property string $name
 * @property string $address
 * @property string $gps_lat
 * @property string $gps_lng
 * @property string $type
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Town $town
 */
class Location extends Entity
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
        'town_id' => true,
        'name' => true,
        'address' => true,
        'gps_lat' => true,
        'gps_lng' => true,
        'type' => true,
        'created' => true,
        'modified' => true,
        'town' => true,
    ];

    public static function getAvailableTypes(): array
    {
        return ['Parking', 'Supermarché', 'Aire de covoiturage', 'Autre'];
    }
}
