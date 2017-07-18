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

/**
 * Class BinaryFile.
 */
class BinaryFile extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param \eZ\Publish\Core\FieldType\Image\Value $value
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param null|\Symfony\Component\HttpFoundation\File\UploadedFile $data
     */
    public function convertFieldValueFromForm($data)
    {
        if ($data === null) {
            return null;
        }

        $fileData = array(
            'inputUri' => $data->getRealPath(),
            'fileName' => $data->getClientOriginalName(),
            'fileSize' => $data->getSize(),
        );

        return new FileValue($fileData);
    }

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
        $maxFileSize = $fieldDefinition->validatorConfiguration['FileSizeValidator']['maxFileSize'];

        if ($maxFileSize !== false) {
            $options['constraints'][] = new Constraints\File(
                array(
                    'maxSize' => $maxFileSize * Constraints\FileValidator::MB_BYTES,
                )
            );
        }

        // File should not be erased (updated as empty) if nothing is selected in file input
        $this->skipEmptyUpdate($formBuilder, $fieldDefinition->identifier);
        // Used with update for displaying current file
        $options['block_name'] = 'ezforms_binary_file';

        $formBuilder->add($fieldDefinition->identifier, FileType::class, $options);
    }
}
