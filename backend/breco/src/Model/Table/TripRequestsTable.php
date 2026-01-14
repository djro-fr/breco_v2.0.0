<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * TripRequests Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\LocationsTable&\Cake\ORM\Association\BelongsTo $DepartureLocations
 * @property \App\Model\Table\LocationsTable&\Cake\ORM\Association\BelongsTo $ArrivalLocations
 * @property \App\Model\Table\BookingsTable&\Cake\ORM\Association\HasMany $Bookings
 *
 * @method \App\Model\Entity\TripRequest newEmptyEntity()
 * @method \App\Model\Entity\TripRequest newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\TripRequest> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TripRequest get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\TripRequest findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\TripRequest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\TripRequest> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\TripRequest|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\TripRequest saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\TripRequest>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TripRequest>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TripRequest>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TripRequest> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TripRequest>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TripRequest>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\TripRequest>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\TripRequest> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TripRequestsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('trip_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DepartureLocations', [
            'foreignKey' => 'departure_location_id',
            'className' => 'Locations',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArrivalLocations', [
            'foreignKey' => 'arrival_location_id',
            'className' => 'Locations',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Bookings', [
            'foreignKey' => 'trip_request_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->integer('departure_location_id')
            ->notEmptyString('departure_location_id');

        $validator
            ->integer('arrival_location_id')
            ->notEmptyString('arrival_location_id');

        $validator
            ->dateTime('departure_time')
            ->requirePresence('departure_time', 'create')
            ->notEmptyDateTime('departure_time');

        $validator
            ->dateTime('arrival_time')
            ->requirePresence('arrival_time', 'create')
            ->notEmptyDateTime('arrival_time');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);
        $rules->add($rules->existsIn(['departure_location_id'], 'DepartureLocations'), ['errorField' => 'departure_location_id']);
        $rules->add($rules->existsIn(['arrival_location_id'], 'ArrivalLocations'), ['errorField' => 'arrival_location_id']);

        return $rules;
    }
}
