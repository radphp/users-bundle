<?php

namespace Users\Library;

use Rad\Authentication\Auth;
use Rad\Authorization\Rbac;

trait AuthorizationTrait
{
    /**
     * Needs authentication
     *
     * @return bool
     */
    public function needsAuthentication()
    {
        return true;
    }

    /**
     * Check user is authorized
     *
     * @return bool
     */
    public function isAuthorized()
    {
        /** @var Rbac $rbac */
        $rbac = $this->getContainer()->get('rbac');

        /** @var Auth $auth */
        $auth = $this->getContainer()->get('auth');
        foreach ($auth->getStorage()->read()['roles'] as $roleName) {
            if (true === $rbac->isGranted($roleName, 'categories.manage')) {
                return true;
            }
        }

        return false;
    }
}
