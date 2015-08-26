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