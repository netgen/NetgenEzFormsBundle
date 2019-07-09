<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use RuntimeException;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateContentType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class UpdateContentTypeTest extends TestCase
{
    public function testItExtendsAbstractType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $this->assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testGetName()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);

        $this->assertEquals('ezforms_update_content', $updateUserType->getName());
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

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperTargetMustBeUser()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\Content');

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

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeUserUpdateStruct()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $content = new Content();

        $options = array('data' => new DataWrapper('payload', 'definition', $content));

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperDefinitionMustBeContentType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $contentUpdateStruct = new ContentUpdateStruct();
        $content = new Content();

        $options = array('data' => new DataWrapper($contentUpdateStruct, null, $content));

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContentTypeIdsMustMatch()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition (ContentType) does not correspond to the data target (Content)');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $contentInfo = new ContentInfo(array('contentTypeId' => 123));
        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));
        $content = new Content(array('versionInfo' => $versionInfo));

        $contentUpdateStruct = new ContentUpdateStruct();

        $contentType = new ContentType(
            array(
                'id' => 654,
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

        $options = array('data' => new DataWrapper($contentUpdateStruct, $contentType, $content));

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldUpdateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->once())
            ->method('buildFieldUpdateForm');

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

        $contentInfo = new ContentInfo(array('contentTypeId' => 123));
        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));
        $content = new Content(array('versionInfo' => $versionInfo));

        $contentUpdateStruct = new ContentUpdateStruct(
            array(
                'initialLanguageCode' => 'eng-GB',
            )
        );

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

        $options = array('data' => new DataWrapper($contentUpdateStruct, $contentType, $content));

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContinueIfFieldIdentifierIsEzUser()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldUpdateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->never())
            ->method('buildFieldUpdateForm');

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

        $contentInfo = new ContentInfo(array('contentTypeId' => 123));
        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));
        $content = new Content(array('versionInfo' => $versionInfo));

        $contentUpdateStruct = new ContentUpdateStruct(
            array(
                'initialLanguageCode' => 'eng-GB',
            )
        );

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

        $options = array('data' => new DataWrapper($contentUpdateStruct, $contentType, $content));

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
