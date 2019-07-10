<?php

declare(strict_types=1);

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
            'defaultValue' => new CheckboxValue(true),
            'descriptions' => ['fre-FR' => 'fre-FR'],
            'names' => ['fre-FR' => 'fre-FR'],
            'validatorConfiguration' => [],
        ];
    }

    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);

        self::assertInstanceOf(FieldTypeHandler::class, $checkboxHandler);
    }

    public function testConvertFieldValueToForm(): void
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxValue = new CheckboxValue(true);

        $returnedBool = $checkboxHandler->convertFieldValueToForm($checkboxValue);

        self::assertTrue($returnedBool);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxValue = new CheckboxValue(true);

        $returnedBool = $checkboxHandler->convertFieldValueFromForm(true);

        self::assertSame($checkboxValue->bool, $returnedBool->bool);
    }

    public function testBuildFieldCreateForm(): void
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [new Constraints\NotBlank()],
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
            ],
            'data' => true,
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive(
                [
                    $fieldDefinition->identifier, CheckboxType::class, $options,
                ]
            );

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldCreateForm($this->formBuilder, $fieldDefinition, 'eng-GB');
    }

    public function testBuildFieldUpdateForm(): void
    {
        $fieldDefinition = new FieldDefinition($this->fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [new Constraints\NotBlank()],
            'ezforms' => [
                'description' => null,
                'language_code' => 'eng-GB',
                'fielddefinition' => $fieldDefinition,
                'content' => $this->content,
            ],
        ];

        $this->formBuilder->expects(self::once())
            ->method('add')->withConsecutive([
                $fieldDefinition->identifier, CheckboxType::class, $options,
            ]);

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }

    public function testBuildFieldUpdateFormIfDefaultValueIsNotSet(): void
    {
        $fieldDefinitionParameters = $this->fieldDefinitionParameters;
        $fieldDefinitionParameters['defaultValue'] = null;
        $fieldDefinition = new FieldDefinition($fieldDefinitionParameters);

        $options = [
            'label' => null,
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
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
                $fieldDefinition->identifier, CheckboxType::class, $options,
            ]);

        $checkboxHandler = new Checkbox($this->fieldHelper);
        $checkboxHandler->buildFieldUpdateForm($this->formBuilder, $fieldDefinition, $this->content, 'eng-GB');
    }
}
