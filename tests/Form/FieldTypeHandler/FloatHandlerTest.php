<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\FloatHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

class FloatHandlerTest extends TestCase
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
            'defaultValue' => new FloatValue(4.74),
            'descriptions' => array('fre-FR' => 'fre-FR'),
            'names' => array('fre-FR' => 'fre-FR'),
            'validatorConfiguration' => array(
                'FloatValueValidator' => array(
                    'minFloatValue' => 4,
                    'maxFloatValue' => 10,
                ),
            ),
        );
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $float = new FloatHandler($this->fieldHelper);

        $this->assertInstanceOf(FieldTypeHandler::class, $float);
    }

    public function testConvertFieldValueToForm()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(4.74);

        $returnedValue = $float->convertFieldValueToForm($floatValue);

        $this->assertEquals(4.74, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(4.74);

        $returnedValue = $float->convertFieldValueFromForm(4.74);

        $this->assertEquals($floatValue, $returnedValue);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotNumeric()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(null);

        $returnedValue = $float->convertFieldValueFromForm('test');

        $this->assertEquals($floatValue, $returnedValue);
    }

    public function testBuildFieldCreateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = array(
            'label' => null,
            'required' => true,
            'constraints' => array(
                new Constraints\NotBlank(),
                new Constraints\Range(array('min' => 4, 'max' => 10)),
            ),
            'ezforms' => array(
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
            ),
            'data' => 4.74,
        );

        $this->formBuilder->expects($this->once())
            ->method('add')->withConsecutive(
                array(
                    $fieldDefinition->identifier, NumberType::class, $options,
                ));

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldCreateForm($this->formBuilder, $fieldDefinition, 'eng-GB');
    }

    public function testBuildFieldUpdateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = array(
            'label' => null,
            'required' => true,
            'constraints' => array(
                new Constraints\NotBlank(),
                new Constraints\Range(array('min' => 4, 'max' => 10)),
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
                $fieldDefinition->identifier, NumberType::class, $options,
            ));

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
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
                new Constraints\Range(array('min' => 4, 'max' => 10)),
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
                $fieldDefinition->identifier, NumberType::class, $options,
            ));

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }
}
