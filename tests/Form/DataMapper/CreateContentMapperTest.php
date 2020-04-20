<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use DateTime;
use eZ\Publish\Core\FieldType\TextLine\Value;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\CreateContentMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final class CreateContentMapperTest extends TestCase
{
    /**
     * @var CreateContentMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    /**
     * @var \Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry
     */
    private $registry;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $handler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $propertyAccessor;

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Contracts\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = new FieldTypeHandlerRegistry();
        $this->registry->register('eztext', $this->handler);

        $this->mapper = new CreateContentMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper(): void
    {
        self::assertInstanceOf('\Symfony\Component\Form\DataMapperInterface', $this->mapper);
    }

    public function testMapDataToFormsShouldThrowUnexpectedTypeException(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->mapper->mapDataToForms('data', 'form');
    }

    public function testMapDataToFormsWithoutValidFieldDefinition(): void
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'test',
                                'fieldTypeIdentifier' => 'eztext',
                                'defaultValue' => new Value('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToForms(): void
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'name',
                                'fieldTypeIdentifier' => 'eztext',
                                'defaultValue' => new Value('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToFormsWithoutDataWrapper(): void
    {
        $data = new stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapDataToFormsDefault(): void
    {
        $data = new stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('setData');

        $form->expects(self::exactly(2))
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn(null);

        $this->mapper->mapDataToForms($data, [$form]);
    }

    public function testMapFormsToData(): void
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'name',
                                'fieldTypeIdentifier' => 'eztext',
                                'defaultValue' => new Value('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getData')
            ->willReturn('Some name');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithInvalidFieldDefinition(): void
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'test',
                                'fieldTypeIdentifier' => 'eztext',
                                'defaultValue' => new Value('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $contentCreateStruct = new ContentCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWhenDataIsNull(): void
    {
        $data = null;
        $this->mapper->mapFormsToData([], $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapper(): void
    {
        $data = new stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::exactly(3))
            ->method('getData')
            ->willReturn('Some name');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapperSecondCase(): void
    {
        $data = new stdClass();
        $date = new DateTime();

        $this->propertyAccessor->expects(self::once())
            ->method('getValue')
            ->willReturn($date);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::exactly(2))
            ->method('getData')
            ->willReturn($date);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataUnexpectedData(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $someNumber = 42;

        $this->mapper->mapFormsToData([], $someNumber);
    }

    public function testMapFormsToDataWhenFormIsNotSynchronized(): void
    {
        $data = new stdClass();

        $this->propertyAccessor->expects(self::never())
            ->method('getValue');

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(false);

        $form->expects(self::never())
            ->method('isDisabled');

        $form->expects(self::never())
            ->method('getData');

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithDataSameAsValueInData(): void
    {
        $this->handler->expects(self::never())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $data = new stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped', 'getByReference'])
            ->getMock();

        $config->expects(self::once())
            ->method('getByReference')
            ->willReturn(true);

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMockForAbstractClass();

        $propertyPath->expects(self::never())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects(self::once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects(self::any())
            ->method('getData')
            ->willReturn($data);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->propertyAccessor->expects(self::once())
            ->method('getValue')
            ->willReturn($data);

        $this->mapper->mapFormsToData([$form], $data);
    }

    private function getForm(): MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getData',
                'setData',
                'getPropertyPath',
                'getConfig',
                'isSubmitted',
                'isSynchronized',
                'isDisabled',
            ])
            ->getMock();
    }
}
