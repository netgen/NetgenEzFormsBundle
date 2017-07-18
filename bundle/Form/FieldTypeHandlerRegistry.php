<?php

namespace Netgen\Bundle\EzFormsBundle\Form;

use OutOfBoundsException;
use RuntimeException;

/**
 * Class FieldTypeHandlerRegistry.
 */
class FieldTypeHandlerRegistry
{
    /**
     * Map of registered callable or FieldTypeHandler objects.
     *
     * @var array
     */
    protected $map = array();

    /**
     * Creates a service registry.
     *
     * In $map an array consisting of a mapping of FieldType identifiers to object / callable is expected.
     * In case of callable factory FieldTypeHandler should be returned on execution.
     *
     * @param array $map A map where key is FieldType identifier, and value is a callable factory to get
     *                   the FieldTypeHandler object
     */
    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * Register a $service for FieldType $identifier.
     *
     * @param string $identifier FieldType identifier
     * @param mixed $handler Callable or FieldTypeHandler instance
     */
    public function register($identifier, $handler)
    {
        $this->map[$identifier] = $handler;
    }

    /**
     * Returns a FieldTypeHandler for FieldType $identifier.
     *
     *
     * @param string $identifier The FieldType identifier
     *
     * @throws \OutOfBoundsException
     * @throws \RuntimeException When type is not a FieldTypeHandler instance nor a callable factory
     *
     * @return \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler
     */
    public function get($identifier)
    {
        if (!isset($this->map[$identifier])) {
            throw new OutOfBoundsException("No handler registered for FieldType '{$identifier}'.");
        } elseif (!$this->map[$identifier] instanceof FieldTypeHandler) {
            if (!is_callable($this->map[$identifier])) {
                throw new RuntimeException("FieldTypeHandler '{$identifier}' is not callable nor instance");
            }

            $factory = $this->map[$identifier];
            $this->map[$identifier] = call_user_func($factory);

            if (!$this->map[$identifier] instanceof FieldTypeHandler) {
                throw new RuntimeException(
                    "FieldTypeHandler '{$identifier}' callable did not return a FieldTypeHandler instance, " .
                    'instead: ' . gettype($this->map[$identifier])
                );
            }
        }

        return $this->map[$identifier];
    }
}
