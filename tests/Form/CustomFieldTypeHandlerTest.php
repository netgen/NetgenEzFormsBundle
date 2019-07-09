<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

class CustomFieldTypeHandlerTest extends TestCase
{
    public function testBuildFieldUpdateFormWhenNoImplementedHandler()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not implemented.');

        $formBuilder = $this->getMockForAbstractClass(FormBuilderInterface::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $content = $this->getMockForAbstractClass(Content::class);
        $language = 'eng-GB';

        $handler = new CustomFieldTypeHandler();
        $handler->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $language);
    }
}
