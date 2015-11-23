<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Validator\Constraints;
use eZ\Publish\Core\FieldType\EmailAddress;

class Email extends FieldTypeHandler
{
    /**
     * {@inheritDoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        if ( !empty( $fieldDefinition->validatorConfiguration["EmailAddressValidator"] ) )
        {
            $options["constraints"][] = new Constraints\Email();
        }

        $formBuilder->add( $fieldDefinition->identifier, "email", $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function convertFieldValueToForm( Value $value, FieldDefinition $fieldDefinition )
    {
        return $value->email;
    }

    /**
     * {@inheritDoc}
     *
     * @return \eZ\Publish\Core\FieldType\EmailAddress\Value
     */
    public function convertFieldValueFromForm( $data )
    {
        return new EmailAddress\Value( $data );
    }
}

