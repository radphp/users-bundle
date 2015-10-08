<?php

namespace Users\Responder\Roles;

use App\Responder\AppResponder;
use Twig\Library\TwigResponse;

/**
 * Put Method Responder
 *
 * @package Users\Responder\Roles
 */
class PutMethodResponder extends AppResponder
{
    /**
     * {@inheritdoc}
     *
     * @return TwigResponse
     */
    public function __invoke()
    {
        $form = $this->getData('form');

        return new TwigResponse(
            '@Users/roles/new.twig',
            ['form' => $form->createView()]
        );
    }
}
