<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\InformationCollectionMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class InformationCollectionMapperTest extends TestCase
{
    /**
     * @var InformationCollectionMapper
     */
    private $mapper;

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
        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
                        ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
                        ->getMock();

        $this->registry = new FieldTypeHandlerRegistry();
        $this->registry->register('eztext', $this->handler);

        $this->mapper = new InformationCollectionMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper(): void
    {
        self::assertInstanceOf('\Netgen\Bundle\EzFormsBundle\Form\DataMapper', $this->mapper);
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
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
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

    public function testMapFormsToDataWithoutValidFieldDefinition(): void
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
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
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
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->handler->expects(self::once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
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

    public function testMapDataToFormsWithInvalidFieldDefinition(): void
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
                                'defaultValue' => new TextLineValue('Some name'),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $infoStruct = new InformationCollectionStruct();
        $data = new DataWrapper($infoStruct, $contentType);

        $config = $this->getMockBuilder('Symfony\Component\Form\FormConfigBuilder')
            ->disableOriginalConstructor()
            ->onlyMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyPathInterface')
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

    private function getForm(): MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled'])
            ->getMock();
    }
}
