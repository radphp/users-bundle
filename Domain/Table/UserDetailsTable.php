<?php

namespace Users\Domain\Table;

use Cake\ORM\Table;

/**
 * User Details Table
 *
 * @package Users\Domain\Table
 */
class UserDetailsTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->belongsTo(
            'Users',
            [
                'className' => 'User',
                'foreignKey' => 'user_id',
                'propertyName' => 'user'
            ]
        );
    }
}
