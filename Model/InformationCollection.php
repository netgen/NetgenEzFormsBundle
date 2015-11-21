<?php

namespace Netgen\Bundle\EzFormsBundle\Model;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;

/**
 * Class InformationCollection
 * @package Netgen\Bundle\EzFormsBundle\Model
 */
class InformationCollection implements InformationCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getField( $fieldDefIdentifier )
    {
        if ( property_exists( $this, $fieldDefIdentifier ) )
        {
            return $this->$fieldDefIdentifier;
        }
        throw new PropertyNotFoundException( $fieldDefIdentifier, get_class( $this ));
    }

    /**
     * {@inheritdoc}
     */
    public function setField( $fieldDefIdentifier, $value )
    {
        $this->$fieldDefIdentifier = $value;

        return $this;
    }
}
