<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\ISBN\Value as IsbnValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class Isbn extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): string
    {
        return $value->isbn;
    }

    public function convertFieldValueFromForm($data): IsbnValue
    {
        if (empty($data)) {
            $data = '';
        }

        return new IsbnValue($data);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        if ($fieldDefinition->fieldSettings['isISBN13']) {
            $options['constraints'][] = new Constraints\Isbn(
                [
                    'type' => 'isbn13',
                ]
            );
        } else {
            $options['constraints'][] = new Constraints\Isbn();
        }

        $formBuilder->add($fieldDefinition->identifier, TextType::class, $options);
    }
}
