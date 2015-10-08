<?php

namespace Users\Action\Roles;

use App\Action\AppAction;
use Cake\ORM\TableRegistry;
use Users\Domain\Entity\Role;
use Users\Library\Form;
use DataTable\Column;
use DataTable\DataSource\ServerSide\CakePHP;
use DataTable\Table;
use Twig\Library\Helper as TwigHelper;

/**
 * Get Method Action
 *
 * @package Users\Roles\Action
 */
class GetMethodAction extends AppAction
{
    /**
     * Invoke Action
     *
     * @param int $id Role id
     *
     * @return void
     * @throws \Rad\Core\Exception\BaseException
     */
    public function __invoke($id = null)
    {
        if (null !== $id) {
            $this->getResponder()->setData('form', Form::create()->getRoleForm($this->getRoles($id)));
        } else {
            $this->getResponder()->setData('table', $this->getDataTable());
        }
    }

    /**
     * Get roles
     *
     * @param null $id Role id
     *
     * @return \Cake\ORM\Query|mixed
     */
    protected function getRoles($id = null)
    {
        $rolesTable = TableRegistry::get('Users.Roles');

        $query = $rolesTable->find()
            ->contain('Resources');

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
        function deleteRole(id) {
    if (confirm(\'Delete this role?\')) {
        $.ajax({
            type: "DELETE",
            url: \'roles/\' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = \'users/roles\';
            }
        });
    }
}', 110);

        $table = new Table();
        $col = new Column();
        $col->setTitle('name')
            ->setData('Roles.name');
        $table->addColumn($col);

        $col = new Column();
        $col->setTitle('title')
            ->setData('Roles.title');
        $table->addColumn($col);

        $router = $this->getRouter();
        $col = new Column\Action();
        $col->setManager(
            function (Column\ActionBuilder $action, Role $role) use ($router) {
                /* TODO Admin RBAC */
                if (true) {
                    $action->addAction(
                        'edit',
                        'Edit',
                        $router->generateUrl(['users', 'roles', $role->get('id')])
                    );
                    $action->addAction(
                        'delete',
                        'Delete',
                        'javascript:deleteRole("' . $role->get('id') . '");'
                    );
                }
            }
        )
            ->setTitle('Actions');
        $table->addColumn($col);

        $table->setDataSource(new CakePHP($this->getRoles(), $this->getRequest()->getRequestTarget()));

        return $table;
    }
}
