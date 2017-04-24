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
        $this->assertInstanceOf(AbstractType::class, $userCreateType);
    }

    public function testBuildForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $emailOptions = array(
            'label' => 'E-mail address',
            'constraints' => array(
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ),
        );

        $usernameOptions = array(
            'label' => 'Username',
            'constraints' => array(
                new Constraints\NotBlank(),
            ),
        );

        $passwordConstraints = array(
            new Constraints\NotBlank(),
            new Constraints\Length(
                array(
                    'min' => 10,
                )
            ),
        );

        $passwordOptions = array(
            'type' => PasswordType::class,
            'invalid_message' => 'Both passwords must match.',
            'options' => array(
                'constraints' => $passwordConstraints,
            ),
            'first_options' => array(
                'label' => 'Password',
            ),
            'second_options' => array(
                'label' => 'Repeat password',
            ),
        );

        $formBuilder->expects($this->at(0))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('email', EmailType::class, $emailOptions);

        $formBuilder->expects($this->at(1))
            ->method('add')
            ->willReturn($formBuilder)
            ->with('username', TextType::class, $usernameOptions);

        $formBuilder->expects($this->at(2))
            ->method('add')
            ->with('password', RepeatedType::class, $passwordOptions);

        $userCreateType = new UserCreateType(10);
        $userCreateType->buildForm($formBuilder, array());
    }

    public function testGetName()
    {
        $userCreateType = new UserCreateType(10);
        $this->assertEquals('ezforms_ezuser_create', $userCreateType->getName());
    }
}
