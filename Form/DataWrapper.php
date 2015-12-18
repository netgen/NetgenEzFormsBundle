<?php

namespace Netgen\Bundle\EzFormsBundle\Form;

/**
 * Class DataWrapper.
 */
class DataWrapper
{
    /**
     * One of the eZ Publish structs, like ContentCreateStruct, UserUpdateStruct and so on.
     *
     * @var mixed
     */
    public $payload;

    /**
     * Definition of the target.
     *
     * In case of Content or User target, this must be the corresponding ContentType.
     *
     * @var null|mixed
     */
    public $definition;

    /**
     * The target struct that applies to. E.g. Content, User, Section object and so on.
     *
     * This target makes sense only in update context, when creating target does not
     * exist (yet to be created).
     *
     * @var null|mixed
     */
    public $target;

    /**
     * Construct from payload, target and definition.
     *
     * @param mixed $payload
     * @param null|mixed $target
     * @param null|mixed $definition
     */
    public function __construct($payload, $definition = null, $target = null)
    {
        $this->payload = $payload;
        $this->definition = $definition;
        $this->target = $target;
    }
}
