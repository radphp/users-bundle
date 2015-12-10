<?php

namespace Users;

use Cake\ORM\TableRegistry;
use Rad\Authentication\UserDetails;
use Rad\Authorization\Rbac;
use Rad\Core\AbstractBundle;
use Rad\Configure\Config;
use Rad\Authentication\Auth;
use Rad\Stuff\Admin\Menu;
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
                    $userDetails = new UserDetails(function ($userData) {
                        /** @var RolesTable $rolesTable */
                        $rolesTable = TableRegistry::get('Users.Roles');
                        $roles = $rolesTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])
                            ->matching('Users', function ($q) use ($userData) {
                                return $q->where(['Users.id' => $userData['id']]);
                            });

                        $userData['roles'] = $roles->toArray();

                        return $userData;
                    });

                    $authentication = new Auth($storage, $userDetails);

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
        $menuItem1 = (new Menu())
            ->setLabel('Users')
            ->setLink('/admin/bundles/users')
            ->setOrder(100);

        $menuItem2 = (new Menu())
            ->setLabel('Add User')
            ->setLink('/admin/bundles/users/new')
            ->setOrder(110);

        $menuItem3 = (new Menu())
            ->setLabel('Roles')
            ->setLink('/admin/bundles/users/roles')
            ->setOrder(120);

        $menuItem4 = (new Menu())
            ->setLabel('Add Role')
            ->setLink('/admin/bundles/users/roles/new')
            ->setOrder(130);

        $root = new Menu();
        $root->setLabel('Users')
            ->setIcon('fa-file-text')
            ->setOrder(999)
            ->addChild($menuItem1)
            ->addChild($menuItem2)
            ->addChild($menuItem3)
            ->addChild($menuItem4)
            ->setResources(['users.manage'])
            ->setAsRoot();
    }
}
