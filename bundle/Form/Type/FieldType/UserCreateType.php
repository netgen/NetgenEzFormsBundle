<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class UserCreateType extends AbstractType
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
        $usernameOptions = [
            'label' => 'Username',
            'constraints' => [
                new Constraints\NotBlank(),
            ],
        ];

        $passwordConstraints = [
            new Constraints\NotBlank(),
        ];

        if ($this->minimumPasswordLength > 0) {
            $passwordConstraints[] = new Constraints\Length(
                [
                    'min' => $this->minimumPasswordLength,
                ]
            );
        }

        $passwordOptions = [
            'type' => PasswordType::class,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => $passwordConstraints,
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
