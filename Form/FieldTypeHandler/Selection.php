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

/**
 * Class Selection
 * @package Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler
 */
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
        $isMultiple = $fieldDefinition->getFieldSettings()["isMultiple"];

        $options['expanded'] = false;
        $options['multiple'] = false;

        if ( $isMultiple )
        {
            $options['multiple'] = true;
        }

        $options['choice_list'] = new ChoiceList( array_keys($values), array_values($values) );


        $formBuilder->add( $fieldDefinition->identifier, "choice", $options );
    }

	/**
	 * {@inheritDoc}
	 *
	 * @return \eZ\Publish\Core\FieldType\Selection\Value
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
    public function convertFieldValueToForm( Value $value )
    {
        return $value->selection;
    }
}
