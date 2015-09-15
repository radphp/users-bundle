<?php

Cake\ORM\TableRegistry::config(
    'Users.Users',
    [
        'table' => 'users',
        'alias' => 'Users',
        'className' => 'Users\Domain\Table\UsersTable',
        'entityClass' => 'Users\Domain\Entity\User',
    ]
);

Cake\ORM\TableRegistry::config(
    'Users.UserDetails',
    [
        'table' => 'user_details',
        'alias' => 'UserDetails',
        'className' => 'Users\Domain\Table\UserDetailsTable',
        'entityClass' => 'Users\Domain\Entity\UserDetail',
    ]
);