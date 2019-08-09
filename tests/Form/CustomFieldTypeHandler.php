<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

final class CustomFieldTypeHandler extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null): void
    {
    }
}
