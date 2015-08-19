<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class Time extends FieldTypeHandler
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
        $options['widget'] = 'choice';
        $options['with_seconds'] = $useSeconds;
        $options['constraints'][] = new Assert\Time();

        $formBuilder->add( $fieldDefinition->identifier, 'time', $options );
    }

    /**
     * {@inheritDoc}
     *
     * @return int|null
     */
    public function convertFieldValueToForm( Value $value )
    {
        $time = $value->time;
        if ( is_int( $time ) )
        {
            return new DateTime( "@$time" );
        }

        return new DateTime();
    }

    /**
     * {@inheritDoc}
     *
     * @return TimeValue
     */
    public function convertFieldValueFromForm( $data )
    {
        if ( $data instanceof DateTime )
        {
            return TimeValue::fromDateTime( $data );
        }

        if ( is_int( $data ) )
        {
            return new TimeValue( $data );
        }

        return new TimeValue( null );
    }
}
