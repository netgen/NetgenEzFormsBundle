<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\FormBuilderInterface;

class CustomFieldTypeHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not implemented.
     */
    public function testBuildFieldUpdateFormWhenNoImplementedHandler()
    {
        $formBuilder = $this->getMockForAbstractClass(FormBuilderInterface::class);
        $fieldDefinition = $this->getMock(FieldDefinition::class);
        $content = $this->getMockForAbstractClass(Content::class);
        $language = 'eng-GB';

        $handler = new CustomFieldTypeHandler();
        $handler->buildFieldUpdateForm($formBuilder, $fieldDefinition, $content, $language);
    }
}
