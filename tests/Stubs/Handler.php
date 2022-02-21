<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Stubs;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

final class Handler extends FieldTypeHandler
{
    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null)
    {
    }
}
