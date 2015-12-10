<?php

namespace Users\Action\Roles;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Network\Http\Response\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Users\Domain\Entity\Role;
use Users\Domain\Table\RolesTable;
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
     * @param int $id Role id
     *
     * @return RedirectResponse
     */
    public function __invoke($id)
    {
        $formValues = $this->getRequest()->getParsedBody()['form'];
        $form = Form::create()->getRoleForm();
        $this->getResponder()->setData('form', $form);

        $form->handleRequest(new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER));

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RolesTable $rolesTable */
            $rolesTable = TableRegistry::get('Users.Roles');

            /** @var Role $role */
            $role = $rolesTable->get($id);
            $rolesTable->patchEntity(
                $role,
                [
                    'name' => $formValues['name'],
                    'title' => $formValues['title'],
                    'description' => $formValues['description'],
                    'resources' => ['_ids' => $formValues['resources']]
                ],
                ['associated' => 'Resources']
            );

            $rolesTable->save($role);

            return new RedirectResponse($this->getRouter()->generateUrl(['users', 'roles']));
        }
    }
}
