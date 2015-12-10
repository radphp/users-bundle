<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Cryptography\Password\DefaultPassword;
use Rad\Network\Http\Response\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Users\Domain\Entity\User;
use Users\Domain\Entity\UserDetail;
use Users\Domain\Table\UsersTable;
use Users\Library\AuthorizationTrait;
use Users\Library\Form;

/**
 * Put Method Action
 *
 * @package Categories\Action
 */
class PutMethodAction extends AppAction
{
    use AuthorizationTrait;

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

            /** @var User $user */
            $user = $usersTable->get($id, ['contain' => 'UserDetails']);

            $detailsKey = ['first_name', 'middle_name', 'last_name'];
            $tmpDetails = [];
            $currentUserDetails = $user->getUserDetails(null, null, true);

            foreach ($detailsKey as $key) {
                if (array_key_exists($key, $currentUserDetails)) {
                    $tmpDetails[] = [
                        'id' => $currentUserDetails[$key]->get('id'),
                        'user_id' => $id,
                        'key' => $key,
                        'value' => strip_tags($formValues[$key])
                    ];
                } else {
                    $tmpDetails[] = [
                        'user_id' => $id,
                        'key' => $key,
                        'value' => strip_tags($formValues[$key])
                    ];
                }
            }

            $data = [
                'username' => $formValues['username'],
                'email' => $formValues['email'],
                'status' => $formValues['status'],
                'details' => $tmpDetails,
                'roles' => ['_ids' => $formValues['roles']]
            ];

            if (!empty($formValues['password'])) {
                $data['password'] = (new DefaultPassword())->hash($formValues['password']);
            }

            $usersTable->patchEntity($user, $data, ['associated' => ['UserDetails', 'Roles']]);
            $usersTable->save($user);

            return new RedirectResponse($this->getRouter()->generateUrl(['users']));
        }
    }
}
