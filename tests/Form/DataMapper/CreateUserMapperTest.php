<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\User\UserCreateStruct;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\CreateUserMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class CreateUserMapperTest extends TestCase
{
    /**
     * @var CreateUserMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
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
            ->setMethods([])
            ->getMock();

        $this->registry = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->mapper = new CreateUserMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper()
    {
        self::assertInstanceOf(DataMapper::class, $this->mapper);
    }

    public function testMapDataToForms()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::once())
            ->method('get')
            ->with('eztext')
            ->willReturn($this->handler);

        $this->handler->expects(self::once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
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

    public function testMapDataToFormsWithInvalidFieldDefinition()
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
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

    public function testMapFormsToDataFieldTypeIdentifierNotEzUser()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::once())
            ->method('get')
            ->with('eztext')
            ->willReturn($this->handler);

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
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

    public function testMapFormsToData()
    {
        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'ezuser',
                            'defaultValue' => new TextLineValue('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $this->registry->expects(self::once())
            ->method('get')
            ->with('ezuser')
            ->willReturn($this->handler);

        $formData = [
            'username' => 'username',
            'email' => 'email@test.com',
            'password' => 'passw0rd',
        ];

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn($formData);

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
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
            ->willReturn($formData);

        $form->expects(self::once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects(self::once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData([$form], $data);
    }

    public function testMapFormsToDataWithInvalidFieldDefinition()
    {
        $this->expectException(RuntimeException::class);

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'ezuser',
                            'defaultValue' => new TextLineValue('Some name'),
                        ]
                    ),
                ],
            ]
        );

        $userCreateStruct = new UserCreateStruct(['contentType' => $contentType]);
        $data = new DataWrapper($userCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMapped'])
            ->getMock();

        $config->expects(self::once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
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

    private function getForm()
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(['getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled'])
            ->getMock();
    }
}
