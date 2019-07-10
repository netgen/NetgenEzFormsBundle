<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Selection as SelectionValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class Selection extends FieldTypeHandler
{
    public function convertFieldValueFromForm($value): SelectionValue\Value
    {
        return new SelectionValue\Value((array) $value);
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null)
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->selection)) {
                return '';
            }

            return $value->selection[0];
        }

        return $value->selection;
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $values = $fieldDefinition->getFieldSettings()['options'];

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choices'] = array_flip($values);

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, $options);
    }
}
