<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Integer as IntegerValue;
use Symfony\Component\Validator\Constraints as Assert;

class Integer extends FieldTypeHandler
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

        if ( !empty( $fieldDefinition->defaultValue ) )
        {
            $options['data'] = (int)$fieldDefinition->defaultValue->value;
        }

        if ( !empty( $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator'] ) )
        {
            $min = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['minIntegerValue'];
            $max = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['maxIntegerValue'];
            $options['constraints'][] = new Assert\Range(
                array(
                    'min' => $min,
                    'max' => $max,
                )
            );
        }

        $formBuilder->add( $fieldDefinition->identifier, 'integer', $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function convertFieldValueToForm( Value $value )
    {
        return (int)$value->value;
    }

    /**
     * {@inheritDoc}
     *
     * @return IntegerValue\Value
     */
    public function convertFieldValueFromForm( $data )
    {
        if ( !is_int( $data ) )
        {
            $data = null;
        }
        return new IntegerValue\Value( $data );
    }
}
