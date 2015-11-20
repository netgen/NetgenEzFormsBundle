<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use eZ\Publish\Core\FieldType\ISBN\Value as IsbnValue;
use Symfony\Component\Validator\Constraints;

/**
 * Class Isbn
 *
 * @package Netgen\EzFormsBundle\FieldType\FormBuilder
 */
class Isbn extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param IsbnValue $value
     */
    public function convertFieldValueToForm( Value $value )
    {
        return $value->isbn;
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

        return new IsbnValue( $data );
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

        if ( $fieldDefinition->fieldSettings['isISBN13'] )
        {
            $options['constraints'][] = new Constraints\Isbn(
                array(
                    'type' => 'isbn13'
                )
            );
        }
        else
        {
            $options['constraints'][] = new Constraints\Isbn();
        }

        $formBuilder->add( $fieldDefinition->identifier, "text", $options );
    }
}
