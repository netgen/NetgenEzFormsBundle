<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class UserCreateType
 *
 * @package Netgen\EzFormsBundle\FieldType
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
        $usernameOptions = array(
            "label" => "Username",
            "constraints" => array(
                new Constraints\NotBlank(),
            ),
        );

        $passwordConstraints = array(
            new Constraints\NotBlank()
        );

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
            "invalid_message" => "Both passwords must match.",
            "options" => array(
                "constraints" => $passwordConstraints,
            ),
            "first_options" => array(
                "label" => "Password",
            ),
            "second_options" => array(
                "label" => "Repeat password",
            ),
        );

        $builder
            ->add( "email", "email", $emailOptions )
            ->add( "username", "text", $usernameOptions )
            ->add( "password", "repeated", $passwordOptions );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "ezforms_ezuser_create";
    }
}
