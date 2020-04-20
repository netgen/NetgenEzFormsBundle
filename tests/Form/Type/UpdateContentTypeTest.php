<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateContentType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

final class UpdateContentTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        self::assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testBuildFormWithoutDataWrapperMustThrowException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $options = ['data' => 'data'];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperTargetMustBeUser(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\Content');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeUserUpdateStruct(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $content = new Content();

        $options = ['data' => new DataWrapper('payload', 'definition', $content)];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperDefinitionMustBeContentType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $contentUpdateStruct = new ContentUpdateStruct();
        $content = new Content();

        $options = ['data' => new DataWrapper($contentUpdateStruct, null, $content)];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContentTypeIdsMustMatch(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition (ContentType) does not correspond to the data target (Content)');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

        $contentInfo = new ContentInfo(['contentTypeId' => 123]);
        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);
        $content = new Content(['versionInfo' => $versionInfo]);

        $contentUpdateStruct = new ContentUpdateStruct();

        $contentType = new ContentType(
            [
                'id' => 654,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'identifier',
                                'fieldTypeIdentifier' => 'field_type',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($contentUpdateStruct, $contentType, $content)];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildFieldUpdateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::once())
            ->method('buildFieldUpdateForm');

        $handlerRegistry = new FieldTypeHandlerRegistry();
        $handlerRegistry->register('field_type', $fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

        $contentInfo = new ContentInfo(['contentTypeId' => 123]);
        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);
        $content = new Content(['versionInfo' => $versionInfo]);

        $contentUpdateStruct = new ContentUpdateStruct(
            [
                'initialLanguageCode' => 'eng-GB',
            ]
        );

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'identifier',
                                'fieldTypeIdentifier' => 'field_type',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($contentUpdateStruct, $contentType, $content)];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContinueIfFieldIdentifierIsEzUser(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildFieldUpdateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::never())
            ->method('buildFieldUpdateForm');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

        $contentInfo = new ContentInfo(['contentTypeId' => 123]);
        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);
        $content = new Content(['versionInfo' => $versionInfo]);

        $contentUpdateStruct = new ContentUpdateStruct(
            [
                'initialLanguageCode' => 'eng-GB',
            ]
        );

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'identifier',
                                'fieldTypeIdentifier' => 'ezuser',
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($contentUpdateStruct, $contentType, $content)];

        $updateUserType = new UpdateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
