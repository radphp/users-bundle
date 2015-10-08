<?php
namespace Users\Domain\Table;

use Cake\ORM\Table;

/**
 * Roles Table
 *
 * @package Users\Domain\Table
 */
class RolesTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always'
                ]
            ]
        ]);

        $this->belongsToMany(
            'Resources',
            [
                'className' => 'Resources',
                'joinTable' => 'role_resources',
                'foreignKey' => 'role_id',
                'dependent' => true,
                'propertyName' => 'resources'
            ]
        );

        $this->belongsToMany(
            'Users',
            [
                'className' => 'Users',
                'joinTable' => 'user_roles',
                'foreignKey' => 'role_id',
                'dependent' => true,
                'propertyName' => 'users'
            ]
        );
    }
}
