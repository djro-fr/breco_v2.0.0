<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property int|null $town_id
 * @property string $email
 * @property bool $email_verified
 * @property string|null $verification_token
 * @property \Cake\I18n\DateTime|null $verification_token_expires
 * @property string $password
 * @property string $last_name
 * @property string $first_name
 * @property string $phone
 * @property int|null $age
 * @property string|null $gender
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class User extends Entity
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
        'email' => true,
        'email_verified' => true,
        'verification_token' => true,
        'verification_token_expires' => true,
        'password' => true,
        'last_name' => true,
        'first_name' => true,
        'phone' => true,
        'age' => true,
        'gender' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $hidden = [
        'password',
    ];
}
