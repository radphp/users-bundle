<?php

namespace Users;

use Admin\Library\Menu;
use Cake\ORM\TableRegistry;
use Rad\Authorization\Rbac;
use Rad\Core\AbstractBundle;
use Rad\Configure\Config;
use Rad\Authentication\Auth;
use Users\Domain\Entity\Role;
use Users\Domain\Table\RolesTable;
use Users\Event\UsersSubscriber;
use Rad\Authentication\Storage\SessionStorage;

/**
 * Users Bundle
 *
 * @package Users
 */
class UsersBundle extends AbstractBundle
{
    /**
     * {@inheritdoc}
     */
    public function startup()
    {
        $this->getEventManager()->addSubscriber(new UsersSubscriber());
        $this->getEventManager()->attach(Menu::EVENT_GET_MENU, [$this, 'addAdminMenu']);
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfig()
    {
        Config::load(__DIR__ . DS . 'Resource' . DS . 'config' . DS . 'config.php');
    }

    /**
     * {@inheritdoc}
     */
    public function loadService()
    {
        if (false === $this->getContainer()->has('auth')) {
            $this->getContainer()->set(
                'auth',
                function () {
                    $storage = new SessionStorage($this->getContainer()->get('session'));
                    $authentication = new Auth($storage);

                    return $authentication;
                }
            );
        }

        if (false === $this->getContainer()->has('rbac')) {
            $this->getContainer()->set(
                'rbac',
                function () {
                    $rbac = new Rbac();

                    /** @var RolesTable $rolesTable */
                    $rolesTable = TableRegistry::get('Users.Roles');
                    $roles = $rolesTable->find()
                        ->contain('Resources');

                    /** @var Role $role */
                    foreach ($roles as $role) {
                        $resourceCollection = new Rbac\ResourceCollection();
                        /** @var Resource $resource */
                        foreach ($role->get('resources') as $resource) {
                            $resourceCollection->attach(
                                Rbac\Resource::create($resource->get('name'))
                                    ->setTitle($resource->get('title'))
                                    ->setDescription($resource->get('description'))
                            );
                        }

                        $rbac->addRole(
                            Rbac\Role::create($role->get('name'), $resourceCollection)
                                ->setTitle($role->get('title'))
                                ->setDescription($role->get('description'))
                        );
                    }

                    return $rbac;
                }
            );
        }
    }

    /**
     * Add required menu for admin panel
     */
    public function addAdminMenu()
    {
        $parent = Menu::addMenu('Users', 'fa-file-text');
        Menu::addMenu('Users', '', '/admin/bundles/users', 100, $parent);
        Menu::addMenu('Add User', '', '/admin/bundles/users/new', 110, $parent);
        Menu::addMenu('Roles', '', '/admin/bundles/users/roles', 120, $parent);
        Menu::addMenu('Add Role', '', '/admin/bundles/users/roles/new', 130, $parent);

    }
}
