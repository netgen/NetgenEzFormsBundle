<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use Netgen\Bundle\EzFormsBundle\Form\Type\InformationCollectionType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class InformationCollectionTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        self::assertInstanceOf(AbstractType::class, $infoCollectionType);
    }

    public function testGetName(): void
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);

        self::assertSame('ezforms_information_collection', $infoCollectionType->getName());
    }

    public function testBuildFormWithoutDataWrapperMustThrowException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $options = ['data' => 'data'];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperPayloadMustBeInformationCollectionStruct(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperDefinitionMustBeContentType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data definition must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $options = ['data' => new DataWrapper($infoStruct)];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldIsNotInfoCollectorSkipIt(): void
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDataMapper'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => false,
                        ]
                    ),
                ],
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormIfFieldUserSkipIt(): void
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDataMapper'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('setDataMapper');

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'fieldTypeIdentifier' => 'ezuser',
                            'isInfoCollector' => false,
                        ]
                    ),
                ],
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildForm(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildFieldCreateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $handlerRegistry->expects(self::once())
            ->method('get')
            ->willReturn($fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => ['eng-GB'],
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => true,
                        ]
                    ),
                ],
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->setLanguages(['eng-GB']);
        $infoCollectionType->buildForm($formBuilder, $options);
    }

    public function testBuildFormTriggerMainLanguageCodeFromContentType(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildFieldCreateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $handlerRegistry->expects(self::once())
            ->method('get')
            ->willReturn($fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $infoStruct = new InformationCollectionStruct();

        $contentType = new ContentType(
            [
                'id' => 123,
                'mainLanguageCode' => 'eng-GB',
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'identifier',
                            'isInfoCollector' => true,
                        ]
                    ),
                ],
            ]
        );

        $options = ['data' => new DataWrapper($infoStruct, $contentType)];

        $infoCollectionType = new InformationCollectionType($handlerRegistry, $dataMapper);
        $infoCollectionType->setLanguages(['fre-CH']);
        $infoCollectionType->buildForm($formBuilder, $options);
    }
}
