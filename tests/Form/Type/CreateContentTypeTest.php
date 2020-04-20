<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\CreateContentType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

final class CreateContentTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
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

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeContentCreateStruct(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\Content\ContentCreateStruct');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildFieldCreateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = new FieldTypeHandlerRegistry();
        $handlerRegistry->register('field_type', $fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

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
        $contentUpdateStruct = new ContentCreateStruct(['contentType' => $contentType, 'mainLanguageCode' => 'eng-GB']);

        $options = ['data' => new DataWrapper($contentUpdateStruct)];

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormContinueIfFieldIdentifierIsEzUser(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['buildFieldCreateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::never())
            ->method('buildFieldCreateForm');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

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
        $contentUpdateStruct = new ContentCreateStruct(['contentType' => $contentType, 'mainLanguageCode' => 'eng-GB']);

        $options = ['data' => new DataWrapper($contentUpdateStruct)];

        $updateUserType = new CreateContentType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
