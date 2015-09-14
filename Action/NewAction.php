<?php

namespace Users\Action;

use App\Action\AppAction;
use Users\Library\Form;

/**
 * New Page Action
 *
 * @package Categories\Action
 */
class NewAction extends AppAction
{
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
        $this->getResponder()->setData('form', Form::create()->getForm());
    }
}
