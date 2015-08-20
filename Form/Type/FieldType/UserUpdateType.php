<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class UserUpdateType
 *
 * @package Netgen\EzFormsBundle\FieldType
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
    public function __construct( $minimumPasswordLength )
    {
        $this->minimumPasswordLength = $minimumPasswordLength;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $emailOptions = array(
            "label" => "E-mail address",
            "constraints" => array(
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ),
        );

        $passwordConstraints = array();
        if ( $this->minimumPasswordLength > 0 )
        {
            $passwordConstraints[] = new Constraints\Length(
                array(
                    "min" => $this->minimumPasswordLength,
                )
            );
        }

        $passwordOptions = array(
            "type" => "password",
            // Setting required to false enables passing empty passwords for no update,
            // while length constraint still applies if passwords are not empty
            "required" => false,
            "invalid_message" => "Both passwords must match.",
            "options" => array(
                "constraints" => $passwordConstraints,
            ),
            "first_options" => array(
                "label" => "New password (leave empty to keep current password)",
            ),
            "second_options" => array(
                "label" => "Repeat new password",
            ),
        );

        $builder
            ->add( "email", "email", $emailOptions )
            ->add( "password", "repeated", $passwordOptions );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "ezforms_ezuser_update";
    }
}
