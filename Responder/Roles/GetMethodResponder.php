<?php

namespace Users\Responder\Roles;

use App\Responder\AppResponder;
use DataTable\Table;
use Rad\Network\Http\Response\JsonResponse;
use Twig\Library\TwigResponse;

/**
 * Get Method Responder
 *
 * @package Users\Responder\Roles
 */
class GetMethodResponder extends AppResponder
{
    /**
     * Invoke responder
     *
     * @return JsonResponse|TwigResponse
     * @throws \DataTable\Exception
     */
    public function __invoke()
    {
        /** @var Table $table */
        $table = $this->getData('table');
        if ($table instanceof Table && $this->getRequest()->isAjax()) {
            return new JsonResponse($table->getResponse()->toArray());
        } elseif ($table instanceof Table) {
            return new TwigResponse('@Users/roles/index.twig', ['table' => $table->render()]);
        }

        // if it is edit form
        if ($form = $this->getData('form', false)) {
            return new TwigResponse(
                '@Users/roles/new.twig',
                [
                    'form' => $form->createView(),
                    'title' => 'Edit a role',
                ]
            );
        }
    }
}
