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
            ->scalar('first_name')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->boolean('driver')
            ->allowEmptyString('driver');

        $validator
            ->scalar('gender')
            ->allowEmptyString('gender');

        $validator
            ->scalar('zip_code')
            ->allowEmptyString('zip_code');

        $validator
            ->scalar('town')
            ->allowEmptyString('town');

        $validator
            ->scalar('car_model')
            ->allowEmptyString('car_model');

        $validator
            ->scalar('car_color')
            ->allowEmptyString('car_color');

        $validator
            ->integer('car_seat_nb')
            ->allowEmptyString('car_seat_nb');

        return $validator;
    }
}
