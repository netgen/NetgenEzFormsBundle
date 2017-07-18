<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\LocationList;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\LocationService;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\REST\Client\Values\Content\Location;
use eZ\Publish\Core\SignalSlot\Repository;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Relation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class RelationTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $content;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentService;

    /**
     * @var array
     */
    protected $fieldDefinitionParameters;

    protected function setUp()
    {
        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getTranslatedContentNameByContentInfo'))
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
            ->setMethods(array('add'))
            ->getMock();
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        $this->assertInstanceOf(FieldTypeHandler::class, $relation);
    }

    public function testConvertFieldValueToForm()
    {
        $relation = new Relation($this->repository, $this->translationHelper);
        $relationValue = new RelationValue(2);

        $returnedValue = $relation->convertFieldValueToForm($relationValue);

        $this->assertEquals(2, $returnedValue);
    }

    public function testConvertFieldValueToFormNullDestinationContentId()
    {
        $relation = new Relation($this->repository, $this->translationHelper);
        $relationValue = new RelationValue(null);

        $returnedValue = $relation->convertFieldValueToForm($relationValue);

        $this->assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        $returnedValue = $relation->convertFieldValueFromForm(23);

        $this->assertEquals(23, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNull()
    {
        $relation = new Relation($this->repository, $this->translationHelper);

        $returnedValue = $relation->convertFieldValueFromForm(null);

        $this->assertNull($returnedValue);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'fieldSettings' => array(
                    'selectionRoot' => 2,
                    'selectionMethod' => 0,
                ),
            )
        );

        $this->translationHelper->expects($this->atLeastOnce())
            ->method('getTranslatedContentNameByContentInfo')
            ->willReturnOnConsecutiveCalls(
                'test1',
                'test2'
            );

        $location = new Location();
        $this->locationService->expects($this->atLeastOnce())
            ->method('loadLocation')
            ->willReturn($location);

        $this->locationService->expects($this->once())
            ->method('loadLocationChildren')
            ->with($location)
            ->willReturn(new LocationList(array(
                'locations' => array(
                    new Location(array('contentInfo' => new ContentInfo())),
                    new Location(array('contentInfo' => new ContentInfo())),
                ),
            )));

        $selection = new Relation($this->repository, $this->translationHelper);
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, 'eng-GB');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage SelectionRoot must be defined
     */
    public function testBuildFieldCreateFormNullSelectionRoot()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->never())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'fieldSettings' => array(
                    'selectionRoot' => null,
                    'selectionMethod' => 0,
                ),
            )
        );

        $selection = new Relation($this->repository, $this->translationHelper);
        $selection->buildFieldCreateForm($formBuilder, $fieldDefinition, 'eng-GB');
    }
}
