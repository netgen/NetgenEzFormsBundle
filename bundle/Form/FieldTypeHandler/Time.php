<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use DateTime;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Time extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): DateTime
    {
        $time = $value->time;
        if (is_int($time)) {
            return new DateTime("@{$time}");
        }

        return new DateTime();
    }

    public function convertFieldValueFromForm($data): TimeValue
    {
        if ($data instanceof DateTime) {
            return TimeValue::fromDateTime($data);
        }

        if (is_int($data)) {
            return new TimeValue($data);
        }

        return new TimeValue(null);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $useSeconds = $fieldDefinition->getFieldSettings()['useSeconds'];
        $options['input'] = 'datetime';
        $options['widget'] = 'choice';
        $options['with_seconds'] = $useSeconds;
        $options['constraints'][] = new Assert\Time();

        $formBuilder->add($fieldDefinition->identifier, TimeType::class, $options);
    }
}
