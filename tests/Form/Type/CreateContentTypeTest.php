<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use RuntimeException;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\CreateContentType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class CreateContentTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $this->assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testGetName()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);

        $this->assertEquals('ezforms_create_content', $updateUserType->getName());
    }

    public function testBuildFormWithoutDataWrapperMustThrowException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => 'data');

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeContentCreateStruct()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\ContentCreateStruct');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => new DataWrapper('payload'));

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldCreateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $handlerRegistry->expects($this->once())
            ->method('get')
            ->willReturn($fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                        )
                    ),
                ),
            )
        );
        $contentUpdateStruct = new ContentCreateStruct(array('contentType' => $contentType));

        $options = array('data' => new DataWrapper($contentUpdateStruct));

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContinueIfFieldIdentifierIsEzUser()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldCreateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->never())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $handlerRegistry->expects($this->never())
            ->method('get')
            ->willReturn($fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'fieldTypeIdentifier' => 'ezuser',
                        )
                    ),
                ),
            )
        );
        $contentUpdateStruct = new ContentCreateStruct(array('contentType' => $contentType));

        $options = array('data' => new DataWrapper($contentUpdateStruct));

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
