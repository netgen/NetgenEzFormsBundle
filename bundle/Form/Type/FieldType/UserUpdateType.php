<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class UserUpdateType.
 */
class UserUpdateType extends AbstractType
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

        $passwordConstraints = array();
        if ($this->minimumPasswordLength > 0) {
            $passwordConstraints[] = new Constraints\Length(
                array(
                    'min' => $this->minimumPasswordLength,
                )
            );
        }

        $passwordOptions = array(
            'type' => PasswordType::class,
            // Setting required to false enables passing empty passwords for no update,
            // while length constraint still applies if passwords are not empty
            'required' => false,
            'invalid_message' => 'Both passwords must match.',
            'options' => array(
                'constraints' => $passwordConstraints,
            ),
            'first_options' => array(
                'label' => 'New password (leave empty to keep current password)',
            ),
            'second_options' => array(
                'label' => 'Repeat new password',
            ),
        );

        $builder
            ->add('email', EmailType::class, $emailOptions)
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
        return 'ezforms_ezuser_update';
    }
}
