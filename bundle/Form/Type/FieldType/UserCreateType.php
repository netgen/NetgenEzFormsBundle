<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class UserCreateType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailOptions = [
            'label' => 'E-mail address',
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ],
        ];

        $usernameOptions = [
            'label' => 'Username',
            'constraints' => [
                new Constraints\NotBlank(),
            ],
        ];

        $passwordOptions = [
            'type' => PasswordType::class,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => $this->getPasswordConstraints($options['ezforms']['fielddefinition'] ?? null),
            ],
            'first_options' => [
                'label' => 'Password',
            ],
            'second_options' => [
                'label' => 'Repeat password',
            ],
        ];

        $builder
            ->add('email', EmailType::class, $emailOptions)
            ->add('username', TextType::class, $usernameOptions)
            ->add('password', RepeatedType::class, $passwordOptions);
    }

    public function getBlockPrefix(): string
    {
        return 'ezforms_ezuser_create';
    }
}
