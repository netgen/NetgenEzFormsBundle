<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\FieldType\EmailAddress;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\Email;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class EmailTest extends TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $email = new Email();

        $this->assertInstanceOf(FieldTypeHandler::class, $email);
    }

    public function testConvertFieldValueToForm()
    {
        $email = new Email();
        $emailValue = new EmailAddress\Value('some@email.test');

        $returnedEmail = $email->convertFieldValueToForm($emailValue);

        $this->assertEquals('some@email.test', $returnedEmail);
    }

    public function testConvertFieldValueFromForm()
    {
        $email = new Email();
        $emailValue = new EmailAddress\Value('some@email.test');

        $returnedEmail = $email->convertFieldValueFromForm('some@email.test');

        $this->assertEquals($emailValue, $returnedEmail);
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
                'validatorConfiguration' => array('EmailAddressValidator' => true),
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
            )
        );

        $languageCode = 'eng-GB';

        $email = new Email();
        $email->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
