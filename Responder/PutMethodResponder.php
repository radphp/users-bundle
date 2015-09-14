<?php

namespace Users\Responder;

use App\Responder\AppResponder;
use Twig\Library\TwigResponse;

/**
 * Put Method Responder
 *
 * @package Users\Responder
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
            '@Users/new.twig',
            ['form' => $form->createView()]
        );
    }
}
