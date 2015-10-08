<?php

namespace Users\Event;

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
        /** @var Request $request */
        $request = $event->getData()['request'];

        /** @var Auth $authentication */
        $auth = $this->getContainer()->get('auth');

        if ($this->needsAuthentication($event->getSubject())) {
            if (!$auth->isAuthenticated()) {
                $event->setResult(new Response\RedirectResponse(self::LOGIN_ROUTE));
            }

            if (!$this->isAuthorized($event->getSubject())) {
                throw new Forbidden();
            }
        }
    }

    /**
     * Check action is authorized
     *
     * @param Dispatcher $dispatcher
     *
     * @return bool
     */
    protected function isAuthorized(Dispatcher $dispatcher)
    {
        $actionNS = $dispatcher->getActionNamespace();

        if (
            is_callable([$actionNS, 'isAuthorized'])
            && false === (bool)call_user_func([$actionNS, 'isAuthorized'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Needs authentication
     *
     * @param Dispatcher $dispatcher
     *
     * @return bool
     */
    protected function needsAuthentication(Dispatcher $dispatcher)
    {
        $actionNS = $dispatcher->getActionNamespace();

        if (
            is_callable([$actionNS, 'needsAuthentication'])
            && true === (bool)call_user_func([$actionNS, 'needsAuthentication'])
        ) {
            return true;
        }

        return false;
    }
}
