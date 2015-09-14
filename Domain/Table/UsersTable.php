<?php
namespace Users\Domain\Table;

use Cake\ORM\Table;

/**
 * Users Table
 *
 * @package Users\Domain\Table
 */
class UsersTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always'
                ]
            ]
        ]);

        $this->hasMany(
            'UserDetails',
            [
                'className' => 'UserDetails',
                'foreignKey' => 'user_id',
                'dependent' => true,
                'propertyName' => 'details'
            ]
        );
    }
}
