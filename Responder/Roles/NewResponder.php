<?php

namespace Users\Responder\Roles;

use App\Responder\AppResponder;
use Twig\Library\TwigResponse;

/**
 * New Role Responder
 *
 * @package Users\Responder\Roles
 */
class NewResponder extends AppResponder
{
    /**
     * Get method
     *
     * @return TwigResponse
     */
    public function getMethod()
    {
        $form = $this->getData('form');

        return new TwigResponse(
            '@Users/roles/new.twig',
            [
                'form' => $form->createView(),
                'title' => 'Add a new role',
            ]
        );
    }
}
