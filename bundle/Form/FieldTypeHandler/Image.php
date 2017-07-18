<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Class Image.
 */
class Image extends FieldTypeHandler
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

        $imageData = array(
            'inputUri' => $data->getRealPath(),
            'fileName' => $data->getClientOriginalName(),
            'fileSize' => $data->getSize(),
        );

        return new ImageValue($imageData);
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
                    'maxSize' => $maxFileSize,
                )
            );
        }

        // Image should not be erased (updated as empty) if nothing is selected in file input
        $this->skipEmptyUpdate($formBuilder, $fieldDefinition->identifier);

        $options['block_name'] = 'ezforms_image';

        $formBuilder->add($fieldDefinition->identifier, FileType::class, $options);
    }
}
