<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class CustomFieldTypeHandlerTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not implemented.
     */
    public function testBuildFieldUpdateFormWhenNoImplementedHandler()
    {
        $formBuilder = $this->getMockForAbstractClass(FormBuilderInterface::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $content = $this->getMockForAbstractClass(Content::class);
        $language = 'eng-GB';

        $handler = new CustomFieldTypeHandler();
        $handler->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $language);
    }
}
