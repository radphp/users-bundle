<?php

namespace Users\Base;

use App\Action\AppAction;
use Rad\Authentication\Auth;

/**
 * Base Users Action
 *
 * @method Auth getAuth() Get authentication service
 *
 * @package Users\Base
 */
class BaseUsersAction extends AppAction
{
    public $needsAuthentication = true;
}
