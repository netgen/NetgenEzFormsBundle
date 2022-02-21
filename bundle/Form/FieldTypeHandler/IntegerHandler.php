<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Integer as IntegerValue;
use Ibexa\Core\Helper\FieldHelper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use function is_int;

final class IntegerHandler extends FieldTypeHandler
{
    /**
     * @var \Ibexa\Core\Helper\FieldHelper
     */
    protected $fieldHelper;

    public function __construct(FieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): int
    {
        return (int) $value->value;
    }

    public function convertFieldValueFromForm($data): IntegerValue\Value
    {
        if (!is_int($data)) {
            $data = null;
        }

        return new IntegerValue\Value($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if (!$content instanceof Content && $fieldDefinition->defaultValue instanceof IntegerValue\Value) {
            $options['data'] = (int) $fieldDefinition->defaultValue->value;
        }

        if (!empty($fieldDefinition->getValidatorConfiguration()['IntegerValueValidator'])) {
            $rangeConstraints = [];

            $min = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['minIntegerValue'];
            $max = $fieldDefinition->getValidatorConfiguration()['IntegerValueValidator']['maxIntegerValue'];

            if ($min !== null) {
                $rangeConstraints['min'] = $min;
            }

            if ($max !== null) {
                $rangeConstraints['max'] = $max;
            }

            if (!empty($rangeConstraints)) {
                $options['constraints'][] = new Assert\Range($rangeConstraints);
            }
        }

        $formBuilder->add($fieldDefinition->identifier, IntegerType::class, $options);
    }
}
