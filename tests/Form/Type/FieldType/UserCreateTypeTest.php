<?php

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

class UserCreateTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $userCreateType = new UserCreateType(10);
        self::assertInstanceOf(AbstractType::class, $userCreateType);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
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

        $passwordConstraints = [
            new Constraints\NotBlank(),
            new Constraints\Length(
                [
                    'min' => 10,
                ]
            ),
        ];

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

        $userCreateType = new UserCreateType(10);
        $userCreateType->buildForm($formBuilder, []);
    }

    public function testGetName()
    {
        $userCreateType = new UserCreateType(10);
        self::assertSame('ezforms_ezuser_create', $userCreateType->getName());
    }
}
