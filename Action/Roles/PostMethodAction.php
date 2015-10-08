<?php

namespace Users\Action\Roles;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Network\Http\Response\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Users\Domain\Entity\Role;
use Users\Domain\Table\RolesTable;
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
        /** @var RolesTable $rolesTable */
        $rolesTable = TableRegistry::get('Users.Roles');
        $formValues = $this->getRequest()->getParsedBody()['form'];
        $form = Form::create()->getRoleForm();
        $this->getResponder()->setData('form', $form);

        $form->handleRequest(new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER));

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Role $role */
            $role = $rolesTable->newEntity(
                [
                    'name' => $formValues['name'],
                    'title' => $formValues['title'],
                    'resources' => ['_ids' => $formValues['resources']],
                    'description' => $formValues['description']
                ],
                ['associated' => 'Resources']
            );
            $rolesTable->save($role);

            return new RedirectResponse($this->getRouter()->generateUrl(['users', 'roles']));
        }
    }
}
