<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class UserTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $user = new User();

        $this->assertInstanceOf(FieldTypeHandler::class, $user);
    }

    public function testConvertFieldValueToForm()
    {
        $user = new User();
        $userValue = $this->getMockForAbstractClass(Value::class);

        $returnedValue = $user->convertFieldValueToForm($userValue);

        $this->assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $user = new User();

        $returnedValue = $user->convertFieldValueFromForm(array('data'));

        $this->assertEquals(array('data'), $returnedValue);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $user = new User();
        $user->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }

    public function testBuildFieldUpdateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $user = new User();
        $user->buildFieldUpdateForm($formBuilder, $fieldDefinition, new Content(), $languageCode);
    }
}
