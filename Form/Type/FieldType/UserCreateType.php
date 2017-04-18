<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class UserCreateType.
 */
class UserCreateType extends AbstractType
{
    /**
     * @var int
     */
    protected $minimumPasswordLength;

    /**
     * @param int $minimumPasswordLength
     */
    public function __construct($minimumPasswordLength)
    {
        $this->minimumPasswordLength = $minimumPasswordLength;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        );

        if ($this->minimumPasswordLength > 0) {
            $passwordConstraints[] = new Constraints\Length(
                array(
                    'min' => $this->minimumPasswordLength,
                )
            );
        }

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

        $builder
            ->add('email', EmailType::class, $emailOptions)
            ->add('username', TextType::class, $usernameOptions)
            ->add('password', RepeatedType::class, $passwordOptions);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'ezforms_ezuser_create';
    }
}
