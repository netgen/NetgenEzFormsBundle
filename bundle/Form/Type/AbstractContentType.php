<?php

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;

/**
 * Class AbstractContentType.
 */
abstract class AbstractContentType extends AbstractType
{
    /**
     * @var \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry
     */
    protected $fieldTypeHandlerRegistry;

    /**
     * @var \Symfony\Component\Form\DataMapperInterface
     */
    protected $dataMapper;

    /**
     * Construct from fieldtype handler registry and data mapper.
     *
     * @param \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry $fieldTypeHandlerRegistry
     * @param \Symfony\Component\Form\DataMapperInterface $dataMapper
     */
    public function __construct(FieldTypeHandlerRegistry $fieldTypeHandlerRegistry, DataMapperInterface $dataMapper)
    {
        $this->fieldTypeHandlerRegistry = $fieldTypeHandlerRegistry;
        $this->dataMapper = $dataMapper;
    }
}
