<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType;
use Netgen\Bundle\EzFormsBundle\Tests\Stubs\ConfigResolverStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

final class InformationCollectionTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        self::assertInstanceOf(AbstractType::class, $infoCollectionType);
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

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeInformationCollectionStruct(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperDefinitionMustBeContentType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition must be an instance of Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType');

        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
                        ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $options = ['data' => new DataWrapper($infoStruct)];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldIsNotInfoCollectorSkipIt(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

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
                                'isInfoCollector' => false,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldUserSkipIt(): void
    {
        $handlerRegistry = new FieldTypeHandlerRegistry();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setDataMapper'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

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
                                'isInfoCollector' => false,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub()
        );

        $infoCollectionType->buildForm($formBuilder, $options);
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
                        ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => ['eng-GB'],
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'identifier',
                                'fieldTypeIdentifier' => 'field_type',
                                'isInfoCollector' => true,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub(['ibexa.site_access.config' => ['languages' => ['eng-GB']]])
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormTriggerMainLanguageCodeFromContentType(): void
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
                        ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldDefinitions' => new FieldDefinitionCollection(
                    [
                        new FieldDefinition(
                            [
                                'id' => 'id',
                                'identifier' => 'identifier',
                                'fieldTypeIdentifier' => 'field_type',
                                'isInfoCollector' => true,
                            ]
                        ),
                    ]
                ),
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType(
            $handlerRegistry,
            $dataMapper,
            new ConfigResolverStub(['ibexa.site_access.config' => ['languages' => ['fre-CH']]])
        );

        $infoCollectionType->buildForm($formBuilder, $options);
    }
}
