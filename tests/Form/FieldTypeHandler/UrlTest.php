<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use Ibexa\Core\FieldType\Url\Value as UrlValue;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

final class UrlTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler(): void
    {
        $url = new Url();

        self::assertInstanceOf(FieldTypeHandler::class, $url);
    }

    public function testConvertFieldValueToForm(): void
    {
        $url = new Url();
        $timeValue = new UrlValue('link', 'text');

        $returnedValue = $url->convertFieldValueToForm($timeValue);

        self::assertSame(['url' => 'link', 'text' => 'text'], $returnedValue);
    }

    public function testConvertFieldValueFromForm(): void
    {
        $url = new Url();
        $timeValue = new UrlValue('link', 'text');

        $returnedValue = $url->convertFieldValueFromForm(['url' => 'link', 'text' => 'text']);

        self::assertSame($timeValue->link, $returnedValue->link);
        self::assertSame($timeValue->text, $returnedValue->text);
    }

    public function testConvertFieldValueFromFormWhenDataIsNotArray(): void
    {
        $url = new Url();
        $timeValue = new UrlValue(null, null);

        $returnedValue = $url->convertFieldValueFromForm('some string');

        self::assertSame($timeValue->link, $returnedValue->link);
        self::assertSame($timeValue->text, $returnedValue->text);
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

        $url = new Url();
        $url->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
