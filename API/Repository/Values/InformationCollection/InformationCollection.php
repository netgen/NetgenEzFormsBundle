<?php

namespace Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollection;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;

/**
 * Class InformationCollection
 * @package Netgen\Bundle\EzFormsBundle\API\Repository\Values\InformationCollection
 */
abstract class InformationCollection extends ValueObject
{
    /**
     * Get value for given fieldIdentifier
     *
     * @param string $fieldIdentifier
     *
     * @return mixed
     *
     * @throws PropertyNotFoundException
     */
    public function getField( $fieldIdentifier )
    {
        if ( property_exists( $this, $fieldIdentifier ) )
        {
            return $this->$fieldIdentifier;
        }
        throw new PropertyNotFoundException( $fieldIdentifier, get_class( $this ) );
    }

    /**
     * Set given value to selected fieldIdentifier
     *
     * @param string $fieldIdentifier
     * @param string  $value
     *
     * @return $this
     */
    public function setField( $fieldIdentifier, $value )
    {
        $this->$fieldIdentifier = $value;

        return $this;
    }
}
