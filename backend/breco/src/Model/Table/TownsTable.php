<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Towns Model
 *
 * @property \App\Model\Table\LocationsTable&\Cake\ORM\Association\HasMany $Locations
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Town newEmptyEntity()
 * @method \App\Model\Entity\Town newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Town> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Town get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Town findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Town patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Town> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Town|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Town saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Town>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Town>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Town>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Town> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Town>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Town>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Town>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Town> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TownsTable extends Table
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

        $this->setTable('towns');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Locations', [
            'foreignKey' => 'town_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'town_id',
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
            ->scalar('zipcode')
            ->maxLength('zipcode', 5)
            ->requirePresence('zipcode', 'create')
            ->notEmptyString('zipcode');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

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
        $rules->add($rules->isUnique(['zipcode', 'name']), ['errorField' => 'zipcode']);

        return $rules;
    }
}
