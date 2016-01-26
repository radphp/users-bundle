<?php

namespace Users\Action;

use App\Action\AppAction;
use Users\Library\AuthorizationTrait;
use Users\Library\Form;

/**
 * New User Action
 *
 * @package Users\Action
 */
class NewAction extends AppAction
{
    use AuthorizationTrait;

    /**
     * Get method
     *
     * @throws \Rad\Core\Exception\BaseException
     */
    public function getMethod()
    {
        $this->getResponder()->setData('form', Form::create()->getForm());
    }
}
