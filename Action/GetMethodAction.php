<?php

namespace Users\Action;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Users\Library\Form;
use DataTable\Column;
use DataTable\DataSource\ServerSide\CakePHP;
use DataTable\Table;
use Twig\Library\Helper as TwigHelper;
use Users\Domain\Entity\User;

/**
 * Get Method Action
 *
 * @package Users\Action
 */
class GetMethodAction extends AppAction
{
    /**
     * Invoke Action
     *
     * @param int $id User id
     *
     * @return void
     * @throws \Rad\Core\Exception\BaseException
     */
    public function __invoke($id = null)
    {
        if (null !== $id) {
            $this->getResponder()->setData('form', Form::create()->getForm($this->getUsers($id)));
        } else {
            $this->getResponder()->setData('table', $this->getDataTable());
        }
    }

    /**
     * Get users
     *
     * @param null $id User id
     *
     * @return \Cake\ORM\Query|mixed
     */
    protected function getUsers($id = null)
    {
        $usersTable = TableRegistry::get('Users.Users');

        $query = $usersTable->find()
            ->contain('UserDetails');

        if (null !== $id) {
            return $query->where(['id' => $id])
                ->first();
        }

        return $query;
    }

    /**
     * Get DataTable
     *
     * @return Table
     * @throws \DataTable\Exception
     */
    protected function getDataTable()
    {
        TwigHelper::addCss('file:///Admin/vendor/datatables/media/css/jquery.dataTables.min.css', 100);
        TwigHelper::addJs('file:///Admin/vendor/jquery/dist/jquery.min.js', 20);
        TwigHelper::addJs('file:///Admin/vendor/datatables/media/js/jquery.dataTables.min.js', 100);
        TwigHelper::addJs('
        function deleteUser(id) {
    if (confirm(\'Delete this user?\')) {
        $.ajax({
            type: "DELETE",
            url: \'users/\' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = \'users\';
            }
        });
    }
}', 110);

        $table = new Table();
        $col = new Column();
        $col->setTitle('Username')
            ->setData('Users.username');
        $table->addColumn($col);

        $col = new Column();
        $col->setTitle('Email')
            ->setData('Users.email');
        $table->addColumn($col);

        $col = new Column();
        $col->setTitle('Status')
            ->setData('Users.status')
            ->isSearchable(false)
            ->setFormatter(function ($status, User $user) {
                $statuses = [0 => 'Inactive', 1 => 'Active', 2 => 'Banned'];

                return $statuses[$status];
            });
        $table->addColumn($col);

        $router = $this->getRouter();
        $col = new Column\Action();
        $col->setManager(
            function (Column\ActionBuilder $action, User $user) use ($router) {
                /* TODO Admin RBAC */
                if (true) {
                    $action->addAction(
                        'edit',
                        'Edit',
                        $router->generateUrl(['users', $user->get('id')])
                    );
                    $action->addAction(
                        'delete',
                        'Delete',
                        'javascript:deleteUser("' . $user->get('id') . '");'
                    );
                }
            }
        )
            ->setTitle('Actions');
        $table->addColumn($col);

        $table->setDataSource(new CakePHP($this->getUsers(), $this->getRequest()->getRequestTarget()));

        return $table;
    }
}
