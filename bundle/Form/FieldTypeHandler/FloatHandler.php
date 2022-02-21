<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Float as FloatValue;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Contracts\Core\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use function is_numeric;

final class FloatHandler extends FieldTypeHandler
{
    /**
     * @var \Ibexa\Core\Helper\FieldHelper
     */
    protected $fieldHelper;

    public function __construct(FieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): float
    {
        return $value->value;
    }

    public function convertFieldValueFromForm($data): FloatValue\Value
    {
        if (!is_numeric($data)) {
            $data = null;
        }

        return new FloatValue\Value($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (!empty($fieldDefinition->getValidatorConfiguration()['FloatValueValidator'])) {
            $rangeConstraints = [];

            $min = $fieldDefinition->getValidatorConfiguration()['FloatValueValidator']['minFloatValue'];
            $max = $fieldDefinition->getValidatorConfiguration()['FloatValueValidator']['maxFloatValue'];

            if ($min !== false) {
                $rangeConstraints['min'] = $min;
            }

            if ($max !== false) {
                $rangeConstraints['max'] = $max;
            }

            if (!empty($rangeConstraints)) {
                $options['constraints'][] = new Assert\Range($rangeConstraints);
            }
        }

        if (!$content instanceof Content && $fieldDefinition->defaultValue instanceof FloatValue\Value) {
            $options['data'] = (float) $fieldDefinition->defaultValue->value;
        }

        $formBuilder->add($fieldDefinition->identifier, NumberType::class, $options);
    }
}
