<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form;

use OutOfBoundsException;
use RuntimeException;

final class FieldTypeHandlerRegistry
{
    /**
     * Map of registered callable or FieldTypeHandlerInterface objects.
     *
     * @var array
     */
    private $map = [];

    /**
     * Creates a service registry.
     *
     * In $map an array consisting of a mapping of FieldType identifiers to object / callable is expected.
     * In case of callable factory FieldTypeHandlerInterface should be returned on execution.
     *
     * @param array $map A map where key is FieldType identifier, and value is a callable factory to get
     *                   the FieldTypeHandlerInterface object
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    /**
     * Register a $service for FieldType $identifier.
     *
     * @param string $identifier FieldType identifier
     * @param mixed $handler Callable or FieldTypeHandlerInterface instance
     */
    public function register(string $identifier, $handler): void
    {
        $this->map[$identifier] = $handler;
    }

    /**
     * Returns a FieldTypeHandlerInterface for FieldType $identifier.
     *
     * @throws \OutOfBoundsException
     * @throws \RuntimeException When type is not a FieldTypeHandlerInterface instance nor a callable factory
     */
    public function get(string $identifier): FieldTypeHandlerInterface
    {
        if (!isset($this->map[$identifier])) {
            throw new OutOfBoundsException("No handler registered for FieldType '{$identifier}'.");
        }
        if (!$this->map[$identifier] instanceof FieldTypeHandlerInterface) {
            if (!is_callable($this->map[$identifier])) {
                throw new RuntimeException("FieldTypeHandler '{$identifier}' is not callable nor instance");
            }

            $factory = $this->map[$identifier];
            $this->map[$identifier] = $factory();

            if (!$this->map[$identifier] instanceof FieldTypeHandlerInterface) {
                throw new RuntimeException(
                    "FieldTypeHandler '{$identifier}' callable did not return a FieldTypeHandlerInterface instance, " .
                    'instead: ' . gettype($this->map[$identifier])
                );
            }
        }

        return $this->map[$identifier];
    }
}
