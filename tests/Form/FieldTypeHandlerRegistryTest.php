<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use RuntimeException;
use OutOfBoundsException;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Date;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use PHPUnit\Framework\TestCase;

class FieldTypeHandlerRegistryTest extends TestCase
{
    public function testItThrowsOutOfBoundExceptionWhenGettingNonExistentHandler()
    {
        $this->expectException(OutOfBoundsException::class);

        $registry = new FieldTypeHandlerRegistry(array());
        $registry->get('some_handler');
    }

    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotCallable()
    {
        $this->expectException(RuntimeException::class);

        $registry = new FieldTypeHandlerRegistry(array('some_handler' => 'handler'));
        $registry->get('some_handler');
    }

    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotInstanceOfHandler()
    {
        $this->expectException(RuntimeException::class);

        $registry = new FieldTypeHandlerRegistry(array('some_handler' => function () {
        }));
        $registry->get('some_handler');
    }

    public function testItReturnsValidHandler()
    {
        $handler = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $registry = new FieldTypeHandlerRegistry(array('some_handler' => $handler));

        $this->assertSame($handler, $registry->get('some_handler'));
    }

    public function testItSetsHandler()
    {
        $handler = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $registry = new FieldTypeHandlerRegistry();
        $registry->register('some_handler', $handler);

        $this->assertSame($handler, $registry->get('some_handler'));
    }

    public function testItReturnsValidHandlerWithoutException()
    {
        $handlerData = new Date();

        $handler = function () {
            return new Date();
        };

        $registry = new FieldTypeHandlerRegistry(array('some_handler' => $handler));

        $this->assertEquals($handlerData, $registry->get('some_handler'));
    }
}
