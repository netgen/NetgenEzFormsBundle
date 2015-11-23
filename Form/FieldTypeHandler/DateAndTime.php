<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use Symfony\Component\Validator\Constraints;
use eZ\Publish\Core\FieldType\DateAndTime as DTValue;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class DateAndTime extends FieldTypeHandler
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

        $useSeconds = $fieldDefinition->getFieldSettings()['useSeconds'];
        $options['input'] = 'datetime';
        $options['date_widget'] = 'choice';
        $options['time_widget'] = 'choice';
        $options['with_seconds'] = $useSeconds;
        $options['constraints'][] = new Assert\DateTime();

        $formBuilder->add( $fieldDefinition->identifier, "datetime", $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return DateTime
     */
    public function convertFieldValueToForm( Value $value, FieldDefinition $fieldDefinition )
    {
        return $value->value;
    }

    /**
     * {@inheritDoc}
     *
     * @return DTValue\Value
     */
    public function convertFieldValueFromForm( $data )
    {
        return new DTValue\Value( $data );
    }
}
