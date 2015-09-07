<?php

namespace Users;

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
}
