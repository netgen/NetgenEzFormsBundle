<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type\FieldType;

use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserCreateType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

final class UserCreateTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $userCreateType = new UserCreateType();
        self::assertInstanceOf(AbstractType::class, $userCreateType);
    }

    public function testBuildForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

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
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ],
            'first_options' => [
                'label' => 'Password',
            ],
            'second_options' => [
                'label' => 'Repeat password',
            ],
        ];

        $formBuilder->expects(self::at(0))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('email', EmailType::class, $emailOptions);

        $formBuilder->expects(self::at(1))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('username', TextType::class, $usernameOptions);

        $formBuilder->expects(self::at(2))
            ->method('add')
            ->with('password', RepeatedType::class, $passwordOptions);

        $userCreateType = new UserCreateType();
        $userCreateType->buildForm($formBuilder, []);
    }
}
