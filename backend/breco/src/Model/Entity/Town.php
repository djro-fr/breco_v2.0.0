<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Town Entity
 *
 * @property int $id
 * @property string $name
 * @property string $postal_code
 * @property string $insee_code
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Location[] $locations
 * @property \App\Model\Entity\User[] $users
 */
class Town extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $accessible = [
        'name' => true,
        'postal_code' => true,
        'insee_code' => true,
        'created' => true,
        'modified' => true,
        'locations' => true,
        'users' => true,
    ];
}
