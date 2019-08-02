<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\User\User;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\UpdateUserMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class UpdateUserMapperTest extends TestCase
{
    /**
     * @var UpdateUserMapper
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
            ->getMock();

        $this->registry = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mapper = new UpdateUserMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper(): void
    {
        self::assertInstanceOf(DataMapper::class, $this->mapper);
    }

    public function testMapDataToForms(): void
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

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $user->expects(self::once())
            ->method('getFieldValue')
            ->willReturn(new TextLineValue('Some name'));

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

    public function testMapDataToFormsWithFieldTypeIdentifierEzUser(): void
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

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

    public function testMapDataToFormsWithInvalidFieldDefinition(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Data payload does not contain expected FieldDefinition 'name'");

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

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

    public function testMapFormsToData(): void
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

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

    public function testMapFormsToDataWithFieldTypeIdentifierEzUser(): void
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

        $formData = [
            'username' => 'username',
            'email' => 'email@test.com',
            'password' => 'passw0rd',
        ];

        $this->registry->expects(self::once())
            ->method('get')
            ->with('ezuser')
            ->willReturn($this->handler);

        $this->handler->expects(self::once())
            ->method('convertFieldValueFromForm')
            ->willReturn($formData);

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

    public function testMapFormsToDataWithInvalidFieldIdentifier(): void
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

        $contentUpdateStruct = new ContentUpdateStruct();

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

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

    public function testMapFormsToDataWithFieldTypeIdentifierEzUserAndShouldSkipReturnsTrue(): void
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

        $contentUpdateStruct = new ContentUpdateStruct();

        $userUpdateStruct = new UserUpdateStruct(
            [
                'contentUpdateStruct' => $contentUpdateStruct,
            ]
        );

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFieldValue'])
            ->getMock();

        $data = new DataWrapper($userUpdateStruct, $contentType, $user);

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

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled', 'getRoot',
                ]
            )
            ->getMock();

        $internalForm = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMock();

        $internalForm->expects(self::any())
            ->method('has')
            ->willReturn(true);

        $internalFormSecond = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $internalFormSecond->expects(self::once())
            ->method('getData')
            ->willReturn('yes');

        $internalForm->expects(self::any())
            ->method('get')
            ->willReturn($internalFormSecond);

        $form->method('getRoot')
            ->willReturn($internalForm);

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

    private function getForm(): MockObject
    {
        return $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled',
                ]
            )
            ->getMock();
    }
}
