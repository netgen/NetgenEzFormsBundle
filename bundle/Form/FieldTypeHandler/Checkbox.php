<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Checkbox as CheckboxValue;
use Ibexa\Core\Helper\FieldHelper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class Checkbox extends FieldTypeHandler
{
    /**
     * @var \Ibexa\Core\Helper\FieldHelper
     */
    protected $fieldHelper;

    public function __construct(FieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): bool
    {
        return $value->bool;
    }

    public function convertFieldValueFromForm($data): CheckBoxValue\Value
    {
        return new CheckboxValue\Value($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (!$content instanceof Content && $fieldDefinition->defaultValue instanceof CheckboxValue\Value) {
            $options['data'] = $fieldDefinition->defaultValue->bool;
        }

        $formBuilder->add($fieldDefinition->identifier, CheckboxType::class, $options);
    }
}
