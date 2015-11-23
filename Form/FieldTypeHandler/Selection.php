<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Validator\Constraints;
use eZ\Publish\Core\FieldType\Selection as SelectionValue;

class Selection extends FieldTypeHandler
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
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $values = $fieldDefinition->getFieldSettings()["options"];

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()["isMultiple"];

        $options['choice_list'] = new ChoiceList( array_keys($values), array_values($values) );

        $formBuilder->add( $fieldDefinition->identifier, "choice", $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return SelectionValue\Value
     */
    public function convertFieldValueFromForm( $value )
    {
        return new SelectionValue\Value( (array) $value );
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function convertFieldValueToForm( Value $value, FieldDefinition $fieldDefinition )
    {
        return $value->selection;
    }
}
