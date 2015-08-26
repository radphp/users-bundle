<?php

namespace Users\Action;

use Exception;
use CakeOrm\Library\CakeORMRepository;
use Rad\Authentication\Exception\CredentialInvalidException;
use Rad\Authentication\Exception\IdentityNotFoundException;
use Rad\Authentication\Provider\SimpleAuthentication;
use Rad\DependencyInjection\Container;
use Symfony\Component\Form\Forms;
use Users\Base\BaseUsersAction;

/**
 * Login Action
 *
 * @package Users\Action
 */
class LoginAction extends BaseUsersAction
{
    /**
     * @var bool
     */
    public $needsAuthentication = false;

    /**
     * Get method
     *
     * @throws \Rad\Core\Exception\BaseException
     */
    public function getMethod()
    {
        $this->getResponder()->setData('form', $this->getForm());
    }

    /**
     * Post method
     *
     * @throws \Rad\Core\Exception\BaseException
     */
    public function postMethod()
    {
        $this->getResponder()->setData('form', $this->getForm());
        $request = $this->getRequest()->getParsedBody();

        if (!empty($request)) {
            $simpleAuth = new SimpleAuthentication($request['form']['username'], $request['form']['password']);
            $simpleAuth->setRepository(CakeORMRepository::create()
                ->setScope(['Users.status' => 1]));

            try {
                $this->getAuth()->authenticate($simpleAuth);
                $this->getFlash()->success('You are successfully logged in.');
            } catch (Exception $e) {
                if ($e instanceof IdentityNotFoundException || $e instanceof CredentialInvalidException) {
                    $this->getFlash()->danger('Username or password does not valid.');
                } else {
                    throw $e;
                }
            }
        }
    }

    /**
     * Get form
     *
     * @return \Symfony\Component\Form\Form
     * @throws \Rad\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function getForm()
    {
        $formFactory = Forms::createFormFactory();
        $options = [
            'action' => Container::get('router')->generateUrl(['users', 'login']),
            'method' => 'POST'
        ];

        return $formFactory->createBuilder('form', null, $options)
            ->add('username', 'text', ['required' => true])
            ->add('password', 'password', ['required' => true])
            ->add('login', 'submit')
            ->getForm();
    }
}
