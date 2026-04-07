<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LoginAttempt Entity
 *
 * @property int $id
 * @property string $email
 * @property string $ip_address
 * @property \Cake\I18n\DateTime $attempted_at
 */
class LoginAttempt extends Entity
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
        'email' => true,
        'ip_address' => true,
        'attempted_at' => true,
    ];
}
