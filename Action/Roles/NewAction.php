<?php

namespace Users\Action\Roles;

use App\Action\AppAction;
use Users\Library\AuthorizationTrait;
use Users\Library\Form;

/**
 * New Role Action
 *
 * @package Users\Action\Roles
 */
class NewAction extends AppAction
{
    use AuthorizationTrait;

    /**
     * @var bool
     */
    public $needsAuthentication = true;

    /**
     * Get method
     *
     * @throws \Rad\Core\Exception\BaseException
     */
    public function getMethod()
    {
        $this->getResponder()->setData('form', Form::create()->getRoleForm());
    }
}
