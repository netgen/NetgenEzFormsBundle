<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;

/**
 * Class User
 *
 * @package Netgen\EzFormsBundle\FieldType\FormBuilder
 */
class User extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     */
    public function convertFieldValueToForm( Value $value )
    {
        // Returning null here because user data is mapped in mapper as an exceptional case
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFieldCreateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode );

        $formBuilder->add( $fieldDefinition->identifier, "ezforms_ezuser_create", $options );
    }

    /**
     * {@inheritdoc}
     */
    public function buildFieldUpdateForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        Content $content,
        $languageCode,
        $readOnly = false
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        $formBuilder->add( $fieldDefinition->identifier, "ezforms_ezuser_update", $options );
    }
}
