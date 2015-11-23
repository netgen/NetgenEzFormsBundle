<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;


use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Validator\Constraints as Assert;
use eZ\Publish\Core\FieldType\Date as DateValue;
use DateTime;

class Date extends FieldTypeHandler
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

        $options['input'] = 'datetime';
        $options['widget'] = 'choice';
		$options['constraints'][] = new Assert\Date();

        $formBuilder->add( $fieldDefinition->identifier, "date", $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return DateTime
     */
    public function convertFieldValueToForm( Value $value, FieldDefinition $fieldDefinition )
    {
        return $value->date;
    }

    /**
     * {@inheritDoc}
     *
     * @return DateValue\Value
     */
    public function convertFieldValueFromForm( $data )
    {
        return new DateValue\Value( $data );
    }
}
