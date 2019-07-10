<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class UserUpdateType extends AbstractType
{
    /**
     * @var int
     */
    protected $minimumPasswordLength;

    public function __construct(int $minimumPasswordLength)
    {
        $this->minimumPasswordLength = $minimumPasswordLength;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailOptions = [
            'label' => 'E-mail address',
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ],
        ];

        $passwordConstraints = [];
        if ($this->minimumPasswordLength > 0) {
            $passwordConstraints[] = new Constraints\Length(
                [
                    'min' => $this->minimumPasswordLength,
                ]
            );
        }

        $passwordOptions = [
            'type' => PasswordType::class,
            // Setting required to false enables passing empty passwords for no update,
            // while length constraint still applies if passwords are not empty
            'required' => false,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => $passwordConstraints,
            ],
            'first_options' => [
                'label' => 'New password (leave empty to keep current password)',
            ],
            'second_options' => [
                'label' => 'Repeat new password',
            ],
        ];

        $builder
            ->add('email', EmailType::class, $emailOptions)
            ->add('password', RepeatedType::class, $passwordOptions);
    }

    public function getBlockPrefix(): string
    {
        return 'ezforms_ezuser_update';
    }
}
