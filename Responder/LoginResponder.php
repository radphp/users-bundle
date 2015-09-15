<?php

namespace Users\Responder;

use App\Responder\AppResponder;
use Rad\Authentication\Auth;
use Rad\Configure\Config;
use Rad\Network\Http\Response\RedirectResponse;
use Twig\Library\TwigResponse;

/**
 * Login Responder
 *
 * @method Auth getAuth() Get authentication service
 *
 * @package Users\Responder
 */
class LoginResponder extends AppResponder
{
    /**
     * {@inheritdoc}
     *
     * @return RedirectResponse|TwigResponse
     */
    public function __invoke()
    {
        if ($this->getAuth()->isAuthenticated()) {
            return new RedirectResponse(Config::get('users.authentication.loginRedirect', '/'));
        }

        $form = $this->getData('form');

        return new TwigResponse(
            Config::get('users.loginTwigTemplate', '@Users/login.twig'),
            ['form' => $form->createView()]
        );
    }
}
