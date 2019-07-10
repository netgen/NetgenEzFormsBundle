<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Date;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FieldTypeHandlerRegistryTest extends TestCase
{
    public function testItThrowsOutOfBoundExceptionWhenGettingNonExistentHandler(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $registry = new FieldTypeHandlerRegistry([]);
        $registry->get('some_handler');
    }

    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotCallable(): void
    {
        $this->expectException(RuntimeException::class);

        $registry = new FieldTypeHandlerRegistry(['some_handler' => 'handler']);
        $registry->get('some_handler');
    }

    public function testItThrowsOutOfRuntimeExceptionWhenHandlerIsNotInstanceOfHandler(): void
    {
        $this->expectException(RuntimeException::class);

        $registry = new FieldTypeHandlerRegistry(['some_handler' => static function () {
        }]);
        $registry->get('some_handler');
    }

    public function testItReturnsValidHandler(): void
    {
        $handler = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $registry = new FieldTypeHandlerRegistry(['some_handler' => $handler]);

        self::assertSame($handler, $registry->get('some_handler'));
    }

    public function testItSetsHandler(): void
    {
        $handler = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $registry = new FieldTypeHandlerRegistry();
        $registry->register('some_handler', $handler);

        self::assertSame($handler, $registry->get('some_handler'));
    }

    public function testItReturnsValidHandlerWithoutException(): void
    {
        $handlerData = new Date();

        $handler = static function () use ($handlerData) {
            return $handlerData;
        };

        $registry = new FieldTypeHandlerRegistry(['some_handler' => $handler]);

        self::assertSame($handlerData, $registry->get('some_handler'));
    }
}
