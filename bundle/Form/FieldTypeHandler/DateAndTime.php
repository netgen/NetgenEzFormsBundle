<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use DateTime;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\DateAndTime as DTValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class DateAndTime extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): DateTime
    {
        return $value->value;
    }

    public function convertFieldValueFromForm($data): DTValue\Value
    {
        return new DTValue\Value($data);
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
        $options['date_widget'] = 'choice';
        $options['time_widget'] = 'choice';
        $options['with_seconds'] = $useSeconds;
        $options['constraints'][] = new Assert\DateTime();

        $formBuilder->add($fieldDefinition->identifier, DateTimeType::class, $options);
    }
}
