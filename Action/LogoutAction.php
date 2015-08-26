<?php

namespace Users\Action;

use Users\Base\BaseUsersAction;

/**
 * Logout Action
 *
 * @package Users\Action
 */
class LogoutAction extends BaseUsersAction
{
    /**
     * @var bool
     */
    public $needsAuthentication = false;

    /**
     * Get method
     */
    public function getMethod()
    {
        $this->getSession()->destroy();
    }
}
