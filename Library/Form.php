<?php

namespace Users\Library;

use Cake\ORM\TableRegistry;
use Rad\DependencyInjection\ContainerAwareTrait;
use Rad\Events\EventManagerTrait;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Users\Domain\Entity\Role;
use Users\Domain\Entity\User;
use Users\Domain\Entity\Resource;
use Users\Domain\Table\ResourcesTable;
use Users\Domain\Table\RolesTable;

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
        $data = [];
        if ($user) {
            foreach ($user->getUserDetails() as $key => $value) {
                $data[$key] = $value;
            }

            $data += $user->toArray();

            $data['roles'] = [];
            foreach ($user->get('roles') as $role) {
                $data['roles'][] = $role->get('id');
            }
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

        /** @var RolesTable $rolesTable */
        $rolesTable = TableRegistry::get('Users.Roles');
        $roles = $rolesTable->find();
        $roleList = [];

        /** @var Role $roles */
        foreach ($roles as $role) {
            $title = $role->get('name');
            if (!empty($role->get('title'))) {
                $title = $role->get('title');
            }

            $roleList[$role->get('id')] = $title;
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
                'roles',
                'choice',
                [
                    'choices' => $roleList,
                    'required' => false,
                    'constraints' => [new NotBlank()],
                    'multiple' => true
                ]
            )
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

    /**
     * Get Role form
     *
     * @param Role $role
     *
     * @return \Symfony\Component\Form\Form
     * @throws \Rad\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function getRoleForm(Role $role = null)
    {
        $data = [];
        if ($role) {
            $data = $role->toArray();
            $data['resources'] = [];
            foreach ($role->get('resources') as $resource) {
                $data['resources'][] = $resource->get('id');
            }
        }

        $action = $role ? $this->get('router')->generateUrl(['users', 'roles', $data['id']]) :
            $this->get('router')->generateUrl(['users', 'roles']);

        /** @var FormFactory $formFactory */
        $formFactory = $this->get('form_factory');
        $options = [
            'action' => $action,
            'method' => $role ? 'PUT' : 'POST'
        ];

        /** @var ResourcesTable $resourcesTable */
        $resourcesTable = TableRegistry::get('Users.Resources');
        $resources = $resourcesTable->find();
        $resourceList = [];

        /** @var Resource $resource */
        foreach ($resources as $resource) {
            $title = $resource->get('name');
            if (!empty($resource->get('title'))) {
                $title = $resource->get('title');
            }

            $resourceList[$resource->get('id')] = $title;
        }

        return $formFactory->createBuilder('form', $data, $options)
            ->add('name', 'text', ['required' => false, 'constraints' => [new NotBlank()]])
            ->add('title', 'text', ['required' => false])
            ->add('description', 'textarea', ['required' => false])
            ->add(
                'resources',
                'choice',
                [
                    'choices' => $resourceList,
                    'required' => false,
                    'constraints' => [new NotBlank()],
                    'multiple' => true
                ]
            )
            ->add('submit', 'submit')
            ->getForm();
    }
}
