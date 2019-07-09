<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\DataMapper;

use eZ\Publish\Core\FieldType\TextLine\Value;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataMapper\CreateContentMapper;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class CreateContentMapperTest extends TestCase
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

    protected function setUp(): void
    {
        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

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

        $this->mapper = new CreateContentMapper($this->registry, $this->propertyAccessor);
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

    public function testMapDataToFormsWithoutDataWrapper()
    {
        $data = new \stdClass();

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
            ->method('setData');

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

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
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn(null);

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
            ->method('getData')
            ->willReturn('Some name');

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

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

        $form->expects($this->exactly(3))
            ->method('getData')
            ->willReturn('Some name');

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapFormsToDataWhenDataIsNotDataWrapperSecondCase()
    {
        $data = new \stdClass();
        $date = new \DateTime();

        $this->propertyAccessor->expects($this->once())
            ->method('getValue')
            ->willReturn($date);

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

        $form->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($date);

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData(array($form), $data);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testMapFormsToDataUnexpectedData()
    {
        $someNumber = 42;

        $this->mapper->mapFormsToData(array(), $someNumber);
    }

    public function testMapFormsToDataWhenFormIsNotSynchronized()
    {
        $data = new \stdClass();

        $this->propertyAccessor->expects($this->never())
            ->method('getValue');

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
            ->willReturn(false);

        $form->expects($this->never())
            ->method('isDisabled');

        $form->expects($this->never())
            ->method('getData');

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->mapper->mapFormsToData(array($form), $data);
    }

    public function testMapFormsToDataWithDataSameAsValueInData()
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

        $this->registry->expects($this->any())
            ->method('get')
            ->with('eztext')
            ->will($this->returnValue($this->handler));

        $this->handler->expects($this->never())
            ->method('convertFieldValueFromForm')
            ->willReturn(new TextLineValue('Some name'));

        $contentCreateStruct = new ContentCreateStruct(array('contentType' => $contentType));

        $data = new \stdClass();

        $config = $this->getMockBuilder(FormConfigBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMapped', 'getByReference'))
            ->getMock();

        $config->expects($this->once())
            ->method('getByReference')
            ->willReturn(true);

        $config->expects($this->once())
            ->method('getMapped')
            ->willReturn(true);

        $propertyPath = $this->getMockBuilder(PropertyPathInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__toString'))
            ->getMockForAbstractClass();

        $propertyPath->expects($this->never())
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

        $form->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $form->expects($this->once())
            ->method('getConfig')
            ->willReturn($config);

        $form->expects($this->once())
            ->method('getPropertyPath')
            ->willReturn($propertyPath);

        $this->propertyAccessor->expects($this->once())
            ->method('getValue')
            ->willReturn($data);

        $this->mapper->mapFormsToData(array($form), $data);
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
}
