<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Checkbox as CheckboxValue;

class Checkbox extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        $isRequired = $fieldDefinition->isRequired;
        $defaultValue = $fieldDefinition->defaultValue->bool;

        $options["required"] = $isRequired;
        $options["data"] = $defaultValue;

        $formBuilder->add( $fieldDefinition->identifier, "checkbox", $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean
     */
    public function convertFieldValueToForm( Value $value )
    {
        return $value->bool;
    }

    /**
     * {@inheritDoc}
     *
     * @return CheckboxValue\Value
     */
    public function convertFieldValueFromForm( $data )
    {
        return new CheckboxValue\Value( $data );
    }
}
