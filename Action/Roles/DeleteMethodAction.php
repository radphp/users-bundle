<?php

namespace Users\Action\Roles;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Network\Http\Response;
use Users\Domain\Table\RolesTable;
use Users\Library\AuthorizationTrait;

/**
 * Delete Method Action
 *
 * @package Users\Action
 */
class DeleteMethodAction extends AppAction
{
    use AuthorizationTrait;

    /**
     * Invoke delete method
     *
     * @param int $id Role id
     *
     * @return Response
     */
    public function __invoke($id)
    {
        /** @var RolesTable $rolesTable */
        $rolesTable = TableRegistry::get('Users.Roles');

        return new Response($rolesTable->deleteAll(['id' => $id]));
    }
}
