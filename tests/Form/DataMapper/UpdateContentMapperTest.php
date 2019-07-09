<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\UpdateContentMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class UpdateContentMapperTest extends TestCase
{
    /**
     * @var UpdateContentMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $propertyAccessor;

    protected function setUp(): void
    {
        $this->propertyAccessor = $this->getMockBuilder('Symfony\Component\PropertyAccess\PropertyAccessorInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->registry = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->handler = $this->getMockBuilder('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $this->mapper = new UpdateContentMapper($this->registry, $this->propertyAccessor);
    }

    public function testInstanceOfDataMapper()
    {
        $this->assertInstanceOf(DataMapper::class, $this->mapper);
    }

    public function testMapDataToForms()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $this->registry->expects($this->once())
            ->method('get')
            ->with('eztext')
            ->will($this->returnValue($this->handler));

        $this->handler->expects($this->once())
            ->method('convertFieldValueToForm')
            ->willReturn('Some name');

        $contentUpdateStruct = new ContentUpdateStruct();

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getFieldValue'))
            ->getMock();

        $content->expects($this->once())
            ->method('getFieldValue')
            ->willReturn(new TextLineValue('Some name'));

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('setData');

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, array($form));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapDataToFormsWithInvalidFieldDefinition()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $contentUpdateStruct = new ContentUpdateStruct();

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapDataToForms($data, array($form));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapFormsToDataWithInvalidFieldDefinition()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $contentUpdateStruct = new ContentUpdateStruct();

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getFieldValue'))
            ->getMock();

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapFormsToData()
    {
        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new TextLineValue('Some name'),
                        )
                    ),
                ),
            )
        );

        $this->registry->expects($this->once())
            ->method('get')
            ->with('eztext')
            ->will($this->returnValue($this->handler));

        $formData = array(
            'username' => 'username',
            'email' => 'email@test.com',
            'password' => 'passw0rd',
        );

        $this->handler->expects($this->once())
            ->method('convertFieldValueFromForm')
            ->willReturn($formData);

        $contentUpdateStruct = new ContentUpdateStruct();

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getFieldValue'))
            ->getMock();

        $data = new DataWrapper($contentUpdateStruct, $contentType, $content);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->method('__toString')
            ->willReturn('name');

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isSynchronized')
            ->willReturn(true);

        $form->expects($this->once())
            ->method('isDisabled')
            ->willReturn(false);

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData(array($form), $data);
    }

    private function getForm()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getData', 'setData', 'getPropertyPath', 'getConfig', 'isSubmitted', 'isSynchronized', 'isDisabled'))
            ->getMock();

        return $form;
    }
}
