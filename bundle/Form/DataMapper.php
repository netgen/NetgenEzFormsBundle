<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form;

use DateTime;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use function is_array;
use function is_object;

/**
 * A data mapper using property paths to read/write data.
 */
abstract class DataMapper implements DataMapperInterface
{
    /**
     * @var \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry
     */
    protected $fieldTypeHandlerRegistry;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * Creates a new property path mapper.
     *
     * @param \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry $fieldTypeHandlerRegistry
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(
        FieldTypeHandlerRegistry $fieldTypeHandlerRegistry,
        ?PropertyAccessorInterface $propertyAccessor = null
    ) {
        $this->fieldTypeHandlerRegistry = $fieldTypeHandlerRegistry;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function mapDataToForms($data, $forms): void
    {
        $empty = null === $data || [] === $data;

        if (!$empty && !is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            if ($data instanceof DataWrapper && null !== $propertyPath && $config->getMapped()) {
                /* @var $data \Netgen\Bundle\EzFormsBundle\Form\DataWrapper */
                $this->mapToForm($form, $data, $propertyPath);
            } elseif (!$empty && null !== $propertyPath && $config->getMapped()) {
                $form->setData($this->propertyAccessor->getValue($data, $propertyPath));
            } else {
                $form->setData($form->getConfig()->getData());
            }
        }
    }

    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            // Write-back is disabled if the form is not synchronized (transformation failed),
            // if the form was not submitted and if the form is disabled (modification not allowed)
            if (
                null === $propertyPath ||
                !$config->getMapped() ||
                !$form->isSubmitted() ||
                !$form->isSynchronized() ||
                $form->isDisabled()
            ) {
                continue;
            }

            // If $data is out ContentCreateStruct, we need to map it to the corresponding field
            // in the struct
            if ($data instanceof DataWrapper) {
                /* @var $data \Netgen\Bundle\EzFormsBundle\Form\DataWrapper */
                $this->mapFromForm($form, $data, $propertyPath);

                continue;
            }

            // If the field is of type DateTime and the data is the same skip the update to
            // keep the original object hash
            if (
                $form->getData() instanceof DateTime &&
                $form->getData() === $this->propertyAccessor->getValue($data, $propertyPath)
            ) {
                continue;
            }

            // If the data is identical to the value in $data, we are
            // dealing with a reference
            if (
                is_object($data) &&
                $config->getByReference() &&
                $form->getData() === $this->propertyAccessor->getValue($data, $propertyPath)
            ) {
                continue;
            }

            $this->propertyAccessor->setValue($data, $propertyPath, $form->getData());
        }
    }

    /**
     * Maps data from Ibexa Platform structure to the form.
     */
    abstract protected function mapToForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void;

    /**
     * Maps data from form to the Ibexa Platform structure.
     */
    abstract protected function mapFromForm(
        FormInterface $form,
        DataWrapper $data,
        PropertyPathInterface $propertyPath
    ): void;

    /**
     * Returns if the update should be skipped for empty value.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param mixed $value
     * @param string $fieldDefinitionIdentifier
     *
     * @return bool
     */
    protected function shouldSkipForEmptyUpdate(FormInterface $form, $value, string $fieldDefinitionIdentifier): bool
    {
        return
            $value === null &&
            (
                $form->getRoot()->has("ezforms_skip_empty_update_{$fieldDefinitionIdentifier}") &&
                $form->getRoot()->get("ezforms_skip_empty_update_{$fieldDefinitionIdentifier}")->getData() === 'yes'
            )
        ;
    }
}
