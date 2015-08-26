<?php

namespace Users\Responder;

use App\Responder\AppResponder;
use Rad\Configure\Config;
use Rad\Network\Http\Response\RedirectResponse;

/**
 * Logout Responder
 *
 * @package Users\Responder
 */
class LogoutResponder extends AppResponder
{
    /**
     * {@inheritdoc}
     *
     * @return RedirectResponse
     */
    public function __invoke()
    {
        return new RedirectResponse(Config::get('users.authentication.logoutRedirect', '/users/login'));
    }
}
