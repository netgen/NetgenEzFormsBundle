<?php

namespace Netgen\Bundle\EzFormsBundle\Core\Repository\Values\InformationCollection;

use Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollection\InformationCollectionStruct as APIInformationCollection;

/**
 * Class InformationCollection
 * @package Netgen\Bundle\EzFormsBundle\Core\Repository\Values\InformationCollection
 */
class InformationCollectionStruct extends APIInformationCollection
{
    /**
     * @var mixed[] An array of field values like $collectedData[$fieldDefIdentifier]
     */
    protected $collectedData;

    /**
     * {@inheritdoc}
     */
    public function getCollectedFieldValue( $fieldDefIdentifier )
    {
        if ( isset( $this->collectedData[$fieldDefIdentifier] ) )
        {
            return $this->collectedData[$fieldDefIdentifier];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectedFields()
    {
        return $this->collectedData;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollectedFieldValue($fieldDefIdentifier, $value)
    {
        $this->collectedData[$fieldDefIdentifier] = $value;
    }
}
