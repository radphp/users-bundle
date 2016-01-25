<?php

namespace Users\Responder;

use App\Responder\AppResponder;
use Twig\Library\TwigResponse;

/**
 * Import User Responder
 *
 * @package Users\Responder
 */
class ImportResponder extends AppResponder
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
            '@Users/import.twig',
            [
                'form' => $form->createView(),
                'title' => 'Import users',
            ]
        );
    }
}
