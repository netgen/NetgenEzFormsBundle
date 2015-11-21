<?php

namespace Netgen\Bundle\EzFormsBundle\Model;

/**
 * Interface InformationCollectionInterface
 * @package Netgen\Bundle\EzFormsBundle\Model
 */
interface InformationCollectionInterface
{
    /**
     * Magic get function handling read to non public properties
     *
     * Returns value for all readonly (protected) properties.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException exception on all reads to undefined properties so typos are not silently accepted.
     *
     * @param string $fieldDefIdentifier the identifier of the field definition
     *
     * @return mixed
     */
    public function getField( $fieldDefIdentifier );

    /**
     * Adds a field to the field collection.
     *
     * @param string $fieldDefIdentifier the identifier of the field definition
     *
     * @param mixed $value Either a plain value which is understandable by the corresponding
     *                     field type or an instance of a Value class provided by the field type
     *
     * @return InformationCollectionInterface
     */
    public function setField( $fieldDefIdentifier, $value );

}
