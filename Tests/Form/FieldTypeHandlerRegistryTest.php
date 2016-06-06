<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Date;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use OutOfBoundsException;
use RuntimeException;

class FieldTypeHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException OutOfBoundsException
     */
    public function testItThrowsOutOfBoundExceptionWhenGettingNonExistentHandler()
    {
        $registry = new FieldTypeHandlerRegistry(array());
        $registry->get('some_handler');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotCallable()
    {
        $registry = new FieldTypeHandlerRegistry(array('some_handler' => 'handler'));
        $registry->get('some_handler');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotInstanceOfHandler()
    {
        $registry = new FieldTypeHandlerRegistry(array('some_handler' => function (){}));
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
}
