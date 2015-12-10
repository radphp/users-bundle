<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Rad\Network\Http\Response;
use Users\Domain\Table\UsersTable;
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
     * @param int $id User id
     *
     * @return Response
     */
    public function __invoke($id)
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::get('Users.Users');

        return new Response($usersTable->deleteAll(['id' => $id]));
    }
}
