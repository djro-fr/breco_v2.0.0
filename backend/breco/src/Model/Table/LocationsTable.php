<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Locations Model
 *
 * @property \App\Model\Table\TownsTable&\Cake\ORM\Association\BelongsTo $Towns
 *
 * @method \App\Model\Entity\Location newEmptyEntity()
 * @method \App\Model\Entity\Location newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Location> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Location findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Location> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Location saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Location>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Location>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Location>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Location>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Location> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LocationsTable extends Table
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

        $this->setTable('locations');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Towns', [
            'foreignKey' => 'town_id',
            'joinType' => 'INNER',
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
            ->integer('town_id')
            ->notEmptyString('town_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('address')
            ->maxLength('address', 255)
            ->requirePresence('address', 'create')
            ->notEmptyString('address');

        $validator
            ->decimal('gps_lat')
            ->requirePresence('gps_lat', 'create')
            ->notEmptyString('gps_lat');

        $validator
            ->decimal('gps_lng')
            ->requirePresence('gps_lng', 'create')
            ->notEmptyString('gps_lng');

        $validator
            ->boolean('carpooling_area')
            ->notEmptyString('carpooling_area');

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
        $rules->add($rules->existsIn(['town_id'], 'Towns'), ['errorField' => 'town_id']);

        return $rules;
    }
}
