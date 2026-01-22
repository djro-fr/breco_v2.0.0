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
 * @method \App\Model\Entity\Location get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Location findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Location saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
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
            ->notEmptyString('gps_lat')
            ->add('gps_lat', 'range', [
                'rule' => function ($value) {
                    // Bretagne : environ 47.0 à 49.0
                    return $value >= 47.0 && $value <= 49.0;
                },
                'message' => 'Coordonnées GPS hors de la Bretagne'
            ]);

        $validator
            ->decimal('gps_lng')
            ->requirePresence('gps_lng', 'create')
            ->notEmptyString('gps_lng')
            ->add('gps_lng', 'range', [
                'rule' => function ($value) {
                    // Bretagne : environ -5.5 à -0.5
                    return $value >= -5.5 && $value <= -0.5;
                },
                'message' => 'Coordonnées GPS hors de la Bretagne'
            ]);

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->requirePresence('type', 'create')
            ->notEmptyString('type')
            ->inList('type', \App\Model\Entity\Location::getAvailableTypes(), 'Type de lieu invalide');

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
        $rules->add($rules->existsIn(['town_id'], 'Towns'), [
            'errorField' => 'town_id',
            'message' => 'La ville sélectionnée n\'existe pas'
        ]);

        return $rules;
    }
}
