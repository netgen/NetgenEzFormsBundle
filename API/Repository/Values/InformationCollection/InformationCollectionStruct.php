<?php

namespace Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollection;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Class InformationCollection
 * @package Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollectionStruct
 */
abstract class InformationCollectionStruct extends ValueObject
{
    /**
     * Returns value for $fieldDefIdentifier
     *
     * @param $fieldDefIdentifier
     *
     * @return mixed
     */
    abstract public function getCollectedFieldValue( $fieldDefIdentifier );

    /**
     * This method returns the complete fields collection
     *
     * @return array
     */
    abstract public function getCollectedFields();

    /**
     * Sets value for $fieldDefIdentifier
     *
     * @param string $fieldDefIdentifier
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function setCollectedFieldValue( $fieldDefIdentifier, $value );
}
