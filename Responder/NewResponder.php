<?php

namespace Users\Responder;

use App\Responder\AppResponder;
use Twig\Library\TwigResponse;

/**
 * New Page Responder
 *
 * @package Users\Responder
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
            '@Users/new.twig',
            [
                'form' => $form->createView(),
                'title' => 'Add a new user',
            ]
        );
    }
}
