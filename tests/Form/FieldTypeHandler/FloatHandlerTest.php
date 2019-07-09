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
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $fieldHelper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $formBuilder;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $content;

    /**
     * @var array
     */
    protected $fieldDefinitionParameters;

    protected function setUp(): void
    {
        $this->fieldHelper = $this->getMockBuilder(FieldHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isFieldEmpty'])
            ->getMock();

        $this->formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $this->content = $this->getMockBuilder('eZ\Publish\API\Repository\Values\Content\Content')
            ->disableOriginalConstructor()
            ->getMock();

        $this->fieldDefinitionParameters = [
            'id' => 'id',
            'identifier' => 'identifier',
            'isRequired' => true,
            'defaultValue' => new FloatValue(4.74),
            'descriptions' => ['fre-FR' => 'fre-FR'],
            'names' => ['fre-FR' => 'fre-FR'],
            'validatorConfiguration' => [
                'FloatValueValidator' => [
                    'minFloatValue' => 4,
                    'maxFloatValue' => 10,
                ],
            ],
        ];
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $float = new FloatHandler($this->fieldHelper);

        self::assertInstanceOf(FieldTypeHandler::class, $float);
    }

    public function testConvertFieldValueToForm()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(4.74);

        $returnedValue = $float->convertFieldValueToForm($floatValue);

        self::assertSame(4.74, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(4.74);

        $returnedValue = $float->convertFieldValueFromForm(4.74);

        self::assertSame($floatValue->value, $returnedValue->value);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotNumeric()
    {
        $float = new FloatHandler($this->fieldHelper);
        $floatValue = new FloatValue(null);

        $returnedValue = $float->convertFieldValueFromForm('test');

        self::assertSame($floatValue->value, $returnedValue->value);
    }

    public function testBuildFieldCreateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Range(['min' => 4, 'max' => 10]),
            ],
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
            ],
            'data' => 4.74,
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive(
                [
                    $fieldDefinition->identifier, NumberType::class, $options,
                ]
            );

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldCreateForm($this->formBuilder, $fieldDefinition, 'eng-GB');
    }

    public function testBuildFieldUpdateForm()
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Range(['min' => 4, 'max' => 10]),
            ],
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
                'content' => $this->content,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive([
                $fieldDefinition->identifier, NumberType::class, $options,
            ]);

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }

    public function testBuildFieldUpdateFormIfDefaultValueIsNotSet()
    {
        $fieldDefinitionParameters = $this->fieldDefinitionParameters;
        $fieldDefinitionParameters['defaultValue'] = null;
        $fieldDefinition = new FieldDefinition($fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Range(['min' => 4, 'max' => 10]),
            ],
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
                'content' => $this->content,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive([
                $fieldDefinition->identifier, NumberType::class, $options,
            ]);

        $floatHandler = new FloatHandler($this->fieldHelper);
        $floatHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }
}
