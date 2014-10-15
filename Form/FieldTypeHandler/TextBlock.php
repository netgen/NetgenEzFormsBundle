<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;

/**
 * Class TextBlock
 *
 * @package Netgen\EzFormsBundle\FieldType\FormBuilder
 */
class TextBlock extends FieldTypeHandler
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
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        $options["attr"]["rows"] = $fieldDefinition->fieldSettings["textRows"];

        $formBuilder->add( $fieldDefinition->identifier, "textarea", $options );
    }
}
