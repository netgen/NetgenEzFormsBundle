<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Checkbox\Value as CheckboxValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Checkbox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

class CheckboxTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $content;

    /**
     * @var array
     */
    protected $fieldDefinitionParameters;

    protected function setUp()
    {
        $this->fieldHelper = $this->getMockBuilder(FieldHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(array('isFieldEmpty'))
            ->getMock();

        $this->formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $this->content = $this->getMockBuilder('eZ\Publish\API\Repository\Values\Content\Content')
            ->disableOriginalConstructor()
            ->getMock();

        $this->fieldDefinitionParameters = array(
            'id' => 'id',
            'identifier' => 'identifier',
            'isRequired' => true,
            'defaultValue' => new CheckboxValue(true),
            'descriptions' => array('fre-FR' => 'fre-FR'),
            'names' => array('fre-FR' => 'fre-FR'),
            'validatorConfiguration' => array(),
        );
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);

        $this->assertInstanceOf(FieldTypeHandler::class, $checkboxHandler);
    }

    public function testConvertFieldValueToForm()
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxValue = new CheckboxValue(true);

        $returnedBool = $checkboxHandler->convertFieldValueToForm($checkboxValue);

        $this->assertTrue($returnedBool);
    }

    public function testConvertFieldValueFromForm()
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxValue = new CheckboxValue(true);

        $returnedBool = $checkboxHandler->convertFieldValueFromForm(true);

        $this->assertEquals($checkboxValue, $returnedBool);
    }

    public function testBuildFieldCreateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = array(
            'label' => null,
            'required' => true,
            'constraints' => array(new Constraints\NotBlank()),
            'ezforms' => array(
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
            ),
            'data' => true,
        );

        $this->formBuilder->expects($this->once())
            ->method('add')->withConsecutive(
                array(
                    $fieldDefinition->identifier, CheckboxType::class, $options,
                ));

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldCreateForm($this->formBuilder, $fieldDefinition, 'eng-GB');
    }

    public function testBuildFieldUpdateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = array(
            'label' => null,
            'required' => true,
            'constraints' => array(new Constraints\NotBlank()),
            'ezforms' => array(
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
                'content' => $this->content,
            ),
        );

        $this->formBuilder->expects($this->once())
            ->method('add')->withConsecutive(array(
                $fieldDefinition->identifier, CheckboxType::class, $options,
            ));

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }

    public function testBuildFieldUpdateFormIfDefaultValueIsNotSet()
    {
        $fieldDefinitionParameters = $this->fieldDefinitionParameters;
        $fieldDefinitionParameters['defaultValue'] = null;
        $fieldDefinition = new FieldDefinition($fieldDefinitionParameters);

        $options = array(
            'label' => null,
            'required' => true,
            'constraints' => array(
                new Constraints\NotBlank(),
            ),
            'ezforms' => array(
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
                'content' => $this->content,
            ),
        );

        $this->formBuilder->expects($this->once())
            ->method('add')->withConsecutive(array(
                $fieldDefinition->identifier, CheckboxType::class, $options,
            ));

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }
}
