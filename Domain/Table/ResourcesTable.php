<?php
namespace Users\Domain\Table;

use Cake\ORM\Table;

/**
 * Resources Table
 *
 * @package Users\Domain\Table
 */
class ResourcesTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsToMany(
            'Roles',
            [
                'className' => 'Roles',
                'joinTable' => 'role_resources',
                'foreignKey' => 'resource_id',
                'dependent' => true,
                'propertyName' => 'roles'
            ]
        );
    }
}
