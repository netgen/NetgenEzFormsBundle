<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\SPI\FieldType\Value;
use Symfony\Component\Validator\Constraints;

/**
 * Class TextLine
 *
 * @package Netgen\EzFormsBundle\FieldType\FormBuilder
 */
class TextLine extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param \eZ\Publish\Core\FieldType\TextLine\Value $value
     */
    public function convertFieldValueToForm( Value $value )
    {
        return $value->text;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFieldValueFromForm( $data )
    {
        if ( empty( $data ) )
        {
            $data = '';
        }

        return new TextLineValue( $data );
    }

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

        if ( !empty( $fieldDefinition->validatorConfiguration["StringLengthValidator"] ) )
        {
            $lengthConstraints = array();

            $minStringLength = $fieldDefinition->validatorConfiguration["StringLengthValidator"]["minStringLength"];
            $maxStringLength = $fieldDefinition->validatorConfiguration["StringLengthValidator"]["maxStringLength"];

            if ( $minStringLength !== false )
            {
                $lengthConstraints["min"] = $minStringLength;
            }

            if ( $maxStringLength !== false )
            {
                $lengthConstraints["max"] = $maxStringLength;
            }

            if ( !empty( $lengthConstraints ) )
            {
                $options["constraints"][] = new Constraints\Length( $lengthConstraints );
            }
        }

        $formBuilder->add( $fieldDefinition->identifier, "text", $options );
    }
}
