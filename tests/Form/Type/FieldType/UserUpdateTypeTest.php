<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type\FieldType;

use Netgen\Bundle\EzFormsBundle\Form\Type\FieldType\UserUpdateType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

class UserUpdateTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $updateUserType = new UserUpdateType(10);
        self::assertInstanceOf(AbstractType::class, $updateUserType);
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

        $passwordConstraints[] = new Constraints\Length(
            [
                'min' => 10,
            ]
        );

        $passwordOptions = [
            'type' => PasswordType::class,
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

        $formBuilder->expects(self::at(0))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('email', EmailType::class, $emailOptions);

        $formBuilder->expects(self::at(1))
            ->method('add')
            ->with('password', RepeatedType::class, $passwordOptions);

        $userCreateType = new UserUpdateType(10);
        $userCreateType->buildForm($formBuilder, []);
    }

    public function testGetName()
    {
        $updateUserType = new UserUpdateType(10);
        self::assertSame('ezforms_ezuser_update', $updateUserType->getName());
    }
}
