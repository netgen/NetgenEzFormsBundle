<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Url as UrlValue;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use function is_array;

final class Url extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): array
    {
        return ['url' => $value->link, 'text' => $value->text];
    }

    public function convertFieldValueFromForm($data): UrlValue\Value
    {
        if (!is_array($data)) {
            $data = [];
            $data['url'] = null;
            $data['text'] = null;
        }

        return new UrlValue\Value($data['url'], $data['text']);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $formBuilder->add($fieldDefinition->identifier, UrlType::class, $options);
    }
}
