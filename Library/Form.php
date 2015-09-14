<?php

namespace Users\Library;

use Rad\DependencyInjection\ContainerAwareTrait;
use Rad\Events\EventManagerTrait;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Users\Domain\Entity\User;

/**
 * Form Library
 *
 * @package Users\Library
 */
class Form
{
    use EventManagerTrait;
    use ContainerAwareTrait;

    protected static $instance;

    /**
     * @return self
     */
    public static function create()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Get form
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     * @throws \Rad\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function getForm(User $user = null)
    {
        $data = null;
        if ($user) {
            $data = $user->toArray();
        }

        $action = $user ? $this->get('router')->generateUrl(['users', $data['id']]) :
            $this->get('router')->generateUrl(['users']);

        /** @var FormFactory $formFactory */
        $formFactory = $this->get('form_factory');
        $options = [
            'action' => $action,
            'method' => $user ? 'PUT' : 'POST'
        ];

        $isNew = true;
        if ($data || $this->getContainer()->get('request')->getParsedBody()['_method'] == 'PUT') {
            $isNew = false;
        }

        return $formFactory->createBuilder('form', $data, $options)
            ->add('username', 'text', ['required' => false, 'constraints' => [new NotBlank()]])
            ->add('email', 'text', ['required' => false, 'constraints' => [new NotBlank(), new Email()]])
            ->add('password', 'password', ['required' => false, 'constraints' => [$isNew ? new NotBlank() : null]])
            ->add('confirm_password', 'password', [
                'required' => false,
                'constraints' => [
                    $isNew ? new NotBlank() : null,
                    new Callback(function ($confirmPassword, ExecutionContext $context) use ($isNew) {
                        if ($isNew && $context->getRoot()->get('password')->getData() != $confirmPassword) {
                            $context->addViolation('Entered passwords do not match.');
                        } elseif ($context->getRoot()->get('password')->getData() != $confirmPassword) {
                            $context->addViolation('Entered passwords do not match.');
                        }
                    })
                ]
            ])
            ->add('first_name', 'text', ['required' => false])
            ->add('middle_name', 'text', ['required' => false])
            ->add('last_name', 'text', ['required' => false])
            ->add(
                'status',
                'choice',
                [
                    'choices' => [0 => 'Inactive', 1 => 'Active', 2 => 'Banned'],
                    'required' => false,
                    'constraints' => [new NotBlank()]
                ]
            )
            ->add('submit', 'submit')
            ->getForm();
    }
}
