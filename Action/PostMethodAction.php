<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Cryptography\Password\DefaultPassword;
use Rad\Network\Http\Response\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Users\Domain\Entity\Role;
use Users\Domain\Entity\User;
use Users\Domain\Entity\UserDetail;
use Users\Domain\Table\UsersTable;
use Users\Library\Form;

/**
 * Post Method Action
 *
 * @package Users\Action
 */
class PostMethodAction extends AppAction
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::get('Users.Users');
        $formValues = $this->getRequest()->getParsedBody()['form'];
        $form = Form::create()->getForm();
        $this->getResponder()->setData('form', $form);

        $form->handleRequest(new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER));

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = [];
            foreach ($formValues['roles'] as $role) {
                $roles[] = new Role(['id' => $role]);
            }

            /** @var User $user */
            $user = $usersTable->newEntity();
            $user->set('username', $formValues['username'])
                ->set('email', $formValues['email'])
                ->set('status', $formValues['status'])
                ->set('password', (new DefaultPassword())->hash($formValues['password']))
                ->set('roles', $roles)
                ->set('details',
                    [
                        new UserDetail([
                            'key' => 'first_name',
                            'value' => strip_tags($formValues['first_name'])
                        ]),
                        new UserDetail([
                            'key' => 'middle_name',
                            'value' => strip_tags($formValues['middle_name'])
                        ]),
                        new UserDetail([
                            'key' => 'last_name',
                            'value' => strip_tags($formValues['last_name'])
                        ])
                    ]);

            $usersTable->save($user);

            return new RedirectResponse($this->getRouter()->generateUrl(['users']));
        }
    }
}
