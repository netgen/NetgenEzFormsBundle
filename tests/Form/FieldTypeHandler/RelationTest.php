<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\LocationList;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\LocationService;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\SignalSlot\Repository;
use InvalidArgumentException;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Relation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class RelationTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $translationHelper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $content;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $locationService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $contentService;

    /**
     * @var array
     */
    protected $fieldDefinitionParameters;

    protected function setUp(): void
    {
        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTranslatedContentNameByContentInfo'])
            ->getMock();

        $this->contentService = $this->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->locationService = $this->getMockBuilder(LocationService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository->method('getLocationService')
            ->willReturn($this->locationService);

        $this->repository->method('getContentService')
            ->willReturn($this->contentService);

        $this->formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        self::assertInstanceOf(FieldTypeHandler::class, $relation);
    }

    public function testConvertFieldValueToForm()
    {
        $relation = new Relation($this->repository, $this->translationHelper);
        $relationValue = new RelationValue(2);

        $returnedValue = $relation->convertFieldValueToForm($relationValue);

        self::assertSame(2, $returnedValue);
    }

    public function testConvertFieldValueToFormNullDestinationContentId()
    {
        $relation = new Relation($this->repository, $this->translationHelper);
        $relationValue = new RelationValue(null);

        $returnedValue = $relation->convertFieldValueToForm($relationValue);

        self::assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        $returnedValue = $relation->convertFieldValueFromForm(23);

        self::assertSame(23, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNull()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        $returnedValue = $relation->convertFieldValueFromForm(null);

        self::assertNull($returnedValue);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldSettings' => [
                    'selectionRoot' => 2,
                    'selectionMethod' => 0,
                ],
            ]
        );

        $this->translationHelper->expects(self::atLeastOnce())
            ->method('getTranslatedContentNameByContentInfo')
            ->willReturnOnConsecutiveCalls(
                'test1',
                'test2'
            );

        $location = new Location();
        $this->locationService->expects(self::atLeastOnce())
            ->method('loadLocation')
            ->willReturn($location);

        $this->locationService->expects(self::once())
            ->method('loadLocationChildren')
            ->with($location)
            ->willReturn(new LocationList([
                'locations' => [
                    new Location(['contentInfo' => new ContentInfo()]),
                    new Location(['contentInfo' => new ContentInfo()]),
                ],
            ]));

        $selection = new Relation($this->repository, $this->translationHelper);
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, 'eng-GB');
    }

    public function testBuildFieldCreateFormNullSelectionRoot()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('SelectionRoot must be defined');

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::never())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldSettings' => [
                    'selectionRoot' => null,
                    'selectionMethod' => 0,
                ],
            ]
        );

        $selection = new Relation($this->repository, $this->translationHelper);
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, 'eng-GB');
    }
}
