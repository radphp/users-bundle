<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Cryptography\Password\DefaultPassword;
use Rad\Network\Http\Response\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Users\Domain\Entity\UserDetail;
use Users\Domain\Table\UsersTable;
use Users\Library\Form;

/**
 * Put Method Action
 *
 * @package Categories\Action
 */
class PutMethodAction extends AppAction
{
    /**
     * Invoke put action
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function __invoke($id)
    {
        $formValues = $this->getRequest()->getParsedBody()['form'];
        $form = Form::create()->getForm();
        $this->getResponder()->setData('form', $form);

        $form->handleRequest(new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER));

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UsersTable $usersTable */
            $usersTable = TableRegistry::get('Users.Users');
            $data = [
                'id' => $id,
                'username' => $formValues['username'],
                'email' => $formValues['email'],
                'status' => $formValues['status'],
                'details' => [
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
                ]
            ];

            if (!empty($formValues['password'])) {
                $data['password'] = (new DefaultPassword())->hash($formValues['password']);
            }

            $usersTable->save($usersTable->newEntity($data));

            return new RedirectResponse($this->getRouter()->generateUrl(['users']));
        }
    }
}
