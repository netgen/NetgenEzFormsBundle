<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class UserTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $user = new User();

        self::assertInstanceOf(FieldTypeHandler::class, $user);
    }

    public function testConvertFieldValueToForm(): void
    {
        $user = new User();
        $userValue = $this->getMockForAbstractClass(Value::class);

        $returnedValue = $user->convertFieldValueToForm($userValue);

        self::assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $user = new User();

        $returnedValue = $user->convertFieldValueFromForm(['data']);

        self::assertSame(['data'], $returnedValue);
    }

    public function testBuildFieldCreateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
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
            ]
        );

        $languageCode = 'eng-GB';

        $user = new User();
        $user->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
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
            ]
        );

        $languageCode = 'eng-GB';

        $user = new User();
        $user->buildFieldUpdateForm($formBuilder, $fieldDefinition, new Content(), $languageCode);
    }
}
