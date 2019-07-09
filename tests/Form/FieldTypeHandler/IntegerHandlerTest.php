<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\Integer\Value as IntegerValue;
use eZ\Publish\Core\Helper\FieldHelper;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\IntegerHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

class IntegerHandlerTest extends TestCase
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

        $this->fieldDefinitionParameters = [
            'id' => 'id',
            'identifier' => 'identifier',
            'isRequired' => true,
            'defaultValue' => new IntegerValue(5),
            'descriptions' => ['fre-FR' => 'fre-FR'],
            'names' => ['fre-FR' => 'fre-FR'],
            'validatorConfiguration' => [
                'IntegerValueValidator' => [
                    'minIntegerValue' => 4,
                    'maxIntegerValue' => 10,
                ],
            ],
        ];
    }

    public function testAssertInstanceOfFieldTypeHandler()
    {
        $integer = new IntegerHandler($this->fieldHelper);

        self::assertInstanceOf(FieldTypeHandler::class, $integer);
    }

    public function testConvertFieldValueToForm()
    {
        $integer = new IntegerHandler($this->fieldHelper);
        $integerValue = new IntegerValue(5);

        $returnedValue = $integer->convertFieldValueToForm($integerValue);

        self::assertSame(5, $returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $integer = new IntegerHandler($this->fieldHelper);
        $integerValue = new IntegerValue(5);

        $returnedValue = $integer->convertFieldValueFromForm(5);

        self::assertSame($integerValue->value, $returnedValue->value);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotNumeric()
    {
        $integer = new IntegerHandler($this->fieldHelper);
        $integerValue = new IntegerValue(null);

        $returnedValue = $integer->convertFieldValueFromForm('test');

        self::assertSame($integerValue->value, $returnedValue->value);
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
            'data' => 5,
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive(
                [
                    $fieldDefinition->identifier,
                    IntegerType::class,
                    $options,
                ]
            );

        $languageCode = 'eng-GB';

        $integerHandler = new IntegerHandler($this->fieldHelper);
        $integerHandler->buildFieldCreateForm($this->formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm()
    {
        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->getMock();

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
                'content' => $content,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive([
                $fieldDefinition->identifier, IntegerType::class, $options,
            ]);

        $languageCode = 'eng-GB';

        $integerHandler = new IntegerHandler($this->fieldHelper);
        $integerHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $content, $languageCode);
    }

    public function testBuildFieldUpdateFormIfDefaultValueIsNotSet()
    {
        $fieldDefinitionParameters = $this->fieldDefinitionParameters;
        $fieldDefinitionParameters['defaultValue'] = null;
        $fieldDefinition = new FieldDefinition($fieldDefinitionParameters);

        $content = $this->getMockBuilder(Content::class)
            ->disableOriginalConstructor()
            ->getMock();

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
                'content' => $content,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive([
                $fieldDefinition->identifier, IntegerType::class, $options,
            ]);

        $languageCode = 'eng-GB';

        $integerHandler = new IntegerHandler($this->fieldHelper);
        $integerHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $content, $languageCode);
    }
}
