<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use eZ\Publish\Core\Repository\Values\User\User;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateUserType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

final class UpdateUserTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
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

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperTargetMustBeUser(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\User\User');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeUserUpdateStruct(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\User\UserUpdateStruct');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user = new User();

        $options = ['data' => new DataWrapper('payload', 'definition', $user)];

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
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

        $userUpdateStruct = new UserUpdateStruct();
        $user = new User();

        $options = ['data' => new DataWrapper($userUpdateStruct, null, $user)];

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
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
        $user = new User(['content' => $content]);

        $userUpdateStruct = new UserUpdateStruct();

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

        $options = ['data' => new DataWrapper($userUpdateStruct, $contentType, $user)];

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
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
        $user = new User(['content' => $content]);

        $contentUpdateStruct = new ContentUpdateStruct(
            [
                'initialLanguageCode' => 'eng-GB',
            ]
        );
        $userUpdateStruct = new UserUpdateStruct(['contentUpdateStruct' => $contentUpdateStruct]);

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

        $options = ['data' => new DataWrapper($userUpdateStruct, $contentType, $user)];

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
