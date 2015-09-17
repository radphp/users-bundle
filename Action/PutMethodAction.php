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

            /** @var User $user */
            $user = $usersTable->get($id, ['contain' => 'UserDetails']);
            $userDetailsTable = TableRegistry::get('UserDetails.UserDetails');

            $detailsKey = ['first_name', 'middle_name', 'last_name'];
            $tmpDetails = [];
            foreach ($detailsKey as $key) {
                if (array_key_exists($key, $user->getUserDetails())) {
                    $userDetail = $userDetailsTable->find()
                        ->where(['key' => $key, 'user_id' => $id])
                        ->first();
                    $userDetail->set('value', strip_tags($formValues[$key]));
                } else {
                    $userDetail = new UserDetail([
                        'user_id' => $id,
                        'key' => $key,
                        'value' => strip_tags($formValues[$key])
                    ]);
                }

                $tmpDetails[] = $userDetail;
            }

            $user->set('id', $id)
                ->set('username', $formValues['username'])
                ->set('email', $formValues['email'])
                ->set('status', $formValues['status'])
                ->set('details', $tmpDetails);

            if (!empty($formValues['password'])) {
                $user->set('password', (new DefaultPassword())->hash($formValues['password']));
            }

            $usersTable->save($user);

            return new RedirectResponse($this->getRouter()->generateUrl(['users']));
        }
    }
}
