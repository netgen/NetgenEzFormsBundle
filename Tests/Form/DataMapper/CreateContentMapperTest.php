<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\CreateContentMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Symfony\Component\Form\FormConfigBuilder;
use eZ\Publish\Core\FieldType\TextLine\Value;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;

class CreateContentMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreateContentMapper
     */
    private $mapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dispatcher;

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

    protected function setUp()
    {
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->registry = $this->getMock('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry');
        $this->handler = $this->getMock('Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerInterface');
        $this->mapper = new CreateContentMapper($this->registry, $this->propertyAccessor);
    }

    private function getForm()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getData',
                'setData',
                'getPropertyPath',
                'getConfig',
                'isSubmitted',
                'isSynchronized',
                'isDisabled',
            ))
            ->getMock();

        return $form;
    }

    public function testInstanceOfDataMapper()
    {
        $this->assertInstanceOf('\Symfony\Component\Form\DataMapperInterface', $this->mapper);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testMapDataToFormsShouldThrowUnexpectedTypeException()
    {
        $this->mapper->mapDataToForms('data', 'form');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMapDataToFormsWithoutValidFieldDefinition()
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
                            'defaultValue' => new Value('Some name'),
                        )
                    ),
                ),
            )
        );

        $contentCreateStruct = new ContentCreateStruct(array('contentType' => $contentType));
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
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
                            'defaultValue' => new Value('Some name'),
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

        $contentCreateStruct = new ContentCreateStruct(array('contentType' => $contentType));
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('setData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
    }

    public function testMapDataToFormsWithoutDataWrapper()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('setData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
    }

    public function testMapDataToFormsDefault()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $form = $this->getForm();

        $form->expects($this->once())
            ->method('setData');

        $form->expects($this->exactly(2))
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn(null)
            ->method('getPropertyPath');

        $this->mapper->mapDataToForms($data, array($form));
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
                            'defaultValue' => new Value('Some name'),
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
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $contentCreateStruct = new ContentCreateStruct(array('contentType' => $contentType));
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->once())
            ->willReturn('Some name')
            ->method('getData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
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
                            'identifier' => 'test',
                            'fieldTypeIdentifier' => 'eztext',
                            'defaultValue' => new Value('Some name'),
                        )
                    ),
                ),
            )
        );

        $contentCreateStruct = new ContentCreateStruct(array('contentType' => $contentType));
        $data = new DataWrapper($contentCreateStruct);

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->once())
            ->willReturn('name')
            ->method('__toString');

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapFormsToDataWhenDataIsNull()
    {
        $data = null;
        $this->mapper->mapFormsToData(array(), $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapper()
    {
        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->exactly(3))
            ->willReturn('Some name')
            ->method('getData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapperSecondCase()
    {
        $data = new \stdClass();
        $date = new \DateTime();

        $this->propertyAccessor->expects($this->once())
            ->willReturn($date)
            ->method('getValue');

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped'))
            ->getMock();

        $config->expects($this->once())
            ->willReturn(true)
            ->method('getMapped');

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $form = $this->getForm();

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSubmitted');

        $form->expects($this->once())
            ->willReturn(true)
            ->method('isSynchronized');

        $form->expects($this->once())
            ->willReturn(false)
            ->method('isDisabled');

        $form->expects($this->exactly(2))
            ->willReturn($date)
            ->method('getData');

        $form->expects($this->once())
            ->willReturn($config)
            ->method('getConfig');

        $form->expects($this->once())
            ->willReturn($propertyPath)
            ->method('getPropertyPath');

        $this->mapper->mapFormsToData(array($form), $data);
    }
}
