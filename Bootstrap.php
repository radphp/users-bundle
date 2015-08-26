<?php

namespace Users;

use Rad\Core\Bundle;
use Rad\Configure\Config;
use Rad\Authentication\Auth;
use Users\Event\UsersSubscriber;
use Rad\Authentication\Storage\SessionStorage;

/**
 * Bootstrap
 *
 * @package Users
 */
class Bootstrap extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function startup()
    {
        parent::startup();

        Config::load(__DIR__ . DS . 'Resource' . DS . 'config' . DS . 'config.php');
        $this->getEventManager()->addSubscriber(new UsersSubscriber());

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
}
