<?php

namespace Users\Action;

use Exception;
use CakeOrm\Library\CakeORMRepository;
use Rad\Authentication\Exception\CredentialInvalidException;
use Rad\Authentication\Exception\IdentityNotFoundException;
use Rad\Authentication\Provider\SimpleAuthentication;
use Rad\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
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
     * @var Form
     */
    protected $form;

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

        $this->getForm()->handleRequest(new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER));

        if ($this->getForm()->isSubmitted() && $this->getForm()->isValid()) {
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
        if ($this->form) {
            return $this->form;
        }

        /** @var FormFactory $formFactory */
        $formFactory = $this->getContainer()->get('form_factory');
        $options = [
            'action' => Container::get('router')->generateUrl(['users', 'login']),
            'method' => 'POST'
        ];

        return $this->form = $formFactory->createBuilder('form', null, $options)
            ->add('username', 'text', ['required' => false, 'constraints' => new NotBlank()])
            ->add('password', 'password', ['required' => false, 'constraints' => new NotBlank()])
            ->add('login', 'submit')
            ->getForm();
    }
}
