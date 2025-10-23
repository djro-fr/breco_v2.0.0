<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('users');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('phone')
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone');

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('firstName')
            ->requirePresence('firstName', 'create')
            ->notEmptyString('firstName');

        $validator
            ->scalar('lastName')
            ->requirePresence('lastName', 'create')
            ->notEmptyString('lastName');

        $validator
            ->boolean('driver')
            ->allowEmptyString('driver');

        $validator
            ->scalar('gender')
            ->allowEmptyString('gender');

        $validator
            ->scalar('zipCode')
            ->allowEmptyString('zipCode');

        $validator
            ->scalar('town')
            ->allowEmptyString('town');

        $validator
            ->scalar('carModel')
            ->allowEmptyString('carModel');

        $validator
            ->scalar('carColor')
            ->allowEmptyString('carColor');

        $validator
            ->integer('carSeatNb')
            ->allowEmptyString('carSeatNb');

        return $validator;
    }
}
