<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

final class Image extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): void
    {
    }

    public function convertFieldValueFromForm($data): ?ImageValue
    {
        if ($data === null) {
            return null;
        }

        $imageData = [
            'inputUri' => $data->getRealPath(),
            'fileName' => $data->getClientOriginalName(),
            'fileSize' => $data->getSize(),
        ];

        return new ImageValue($imageData);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $maxFileSize = $fieldDefinition->validatorConfiguration['FileSizeValidator']['maxFileSize'] ?? false;

        if ($maxFileSize !== false) {
            $options['constraints'][] = new Constraints\File(
                [
                    'maxSize' => $maxFileSize,
                ]
            );
        }

        // Image should not be erased (updated as empty) if nothing is selected in file input
        $this->skipEmptyUpdate($formBuilder, $fieldDefinition->identifier);

        $options['block_name'] = 'ezforms_image';

        $formBuilder->add($fieldDefinition->identifier, FileType::class, $options);
    }
}
