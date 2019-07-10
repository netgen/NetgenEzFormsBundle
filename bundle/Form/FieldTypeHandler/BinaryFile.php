<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\BinaryFile\Value as FileValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class BinaryFile extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): void
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $data
     */
    public function convertFieldValueFromForm($data): ?FileValue
    {
        if ($data === null) {
            return null;
        }

        $fileData = [
            'inputUri' => $data->getRealPath(),
            'fileName' => $data->getClientOriginalName(),
            'fileSize' => $data->getSize(),
        ];

        return new FileValue($fileData);
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);
        $maxFileSize = $fieldDefinition->validatorConfiguration['FileSizeValidator']['maxFileSize'];

        if ($maxFileSize !== false) {
            $options['constraints'][] = new Constraints\File(
                [
                    'maxSize' => $maxFileSize * Constraints\FileValidator::MB_BYTES,
                ]
            );
        }

        // File should not be erased (updated as empty) if nothing is selected in file input
        $this->skipEmptyUpdate($formBuilder, $fieldDefinition->identifier);
        // Used with update for displaying current file
        $options['block_name'] = 'ezforms_binary_file';

        $formBuilder->add($fieldDefinition->identifier, FileType::class, $options);
    }
}
