<?php

namespace Users\Event;

use Rad\Core\Action;
use Rad\Events\Event;
use Rad\Network\Http\Exception\Forbidden;
use Rad\Routing\Dispatcher;
use Rad\Authentication\Auth;
use Rad\Events\EventManager;
use Rad\Network\Http\Request;
use Rad\Network\Http\Response;
use Rad\Events\EventSubscriberInterface;
use Rad\DependencyInjection\ContainerAwareTrait;

/**
 * Users Event Subscriber
 *
 * @package Users\Event
 */
class UsersSubscriber implements EventSubscriberInterface
{
    use ContainerAwareTrait;
    const LOGIN_ROUTE = '/users/login';

    /**
     * Subscribe event listener
     *
     * @param EventManager $eventManager
     *
     * @return mixed
     */
    public function subscribe(EventManager $eventManager)
    {
        $eventManager->attach(Dispatcher::EVENT_BEFORE_DISPATCH, [$this, 'authenticate']);
    }

    /**
     * Authenticate
     *
     * @param Event $event
     *
     * @return Response\RedirectResponse
     * @throws Forbidden
     * @throws \Rad\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function authenticate(Event $event)
    {
        /** @var Auth $authentication */
        $auth = $this->getContainer()->get('auth');

        if ($this->needsAuthentication($event->getData()['action'])) {
            if (!$auth->isAuthenticated()) {
                $event->setResult(new Response\RedirectResponse(self::LOGIN_ROUTE));

                return null;
            }

            if (!$this->isAuthorized($event->getData()['action'])) {
                throw new Forbidden();
            }
        }
    }

    /**
     * Check action is authorized
     *
     * @param Action $action
     *
     * @return bool
     */
    protected function isAuthorized(Action $action)
    {
        if (
            is_callable([$action, 'isAuthorized'])
            && false === (bool)call_user_func([$action, 'isAuthorized'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Needs authentication
     *
     * @param Action $action
     *
     * @return bool
     */
    protected function needsAuthentication(Action $action)
    {
        if (
            is_callable([$action, 'needsAuthentication'])
            && true === (bool)call_user_func([$action, 'needsAuthentication'])
        ) {
            return true;
        }

        return false;
    }
}
