<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
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
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $values = $fieldDefinition->getFieldSettings()['options'];

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()['isMultiple'];

        $options['choice_list'] = new ChoiceList(array_keys($values), array_values($values));

        $formBuilder->add($fieldDefinition->identifier, 'choice', $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return SelectionValue\Value
     */
    public function convertFieldValueFromForm($value)
    {
        return new SelectionValue\Value((array) $value);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
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
}
