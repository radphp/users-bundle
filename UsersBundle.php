<?php

namespace Users;

use Admin\Library\Menu;
use Rad\Core\AbstractBundle;
use Rad\Configure\Config;
use Rad\Authentication\Auth;
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
    }

    /**
     * Add required menu for admin panel
     */
    public function addAdminMenu()
    {
        $parent = Menu::addMenu('Users', 'fa-file-text');
        Menu::addMenu('Users', '', '/admin/bundles/users', 100, $parent);
        Menu::addMenu('Add User', '', '/admin/bundles/users/new', 110, $parent);
    }
}
