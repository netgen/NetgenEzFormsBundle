<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\BinaryFile;
use eZ\Publish\SPI\FieldType\Value;
use eZ\Publish\Core\FieldType\BinaryFile\Value as FileValue;
use Symfony\Component\Form\FormBuilder;

class BinaryFileTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertInstanceOfFieldTypeHandler()
    {
        $binaryFile = new BinaryFile();

        $this->assertInstanceOf(FieldTypeHandler::class, $binaryFile);
    }

    public function testConvertFieldValueToForm()
    {
        $binaryFile = new BinaryFile();
        $binaryFileValue = $this->getMockForAbstractClass(Value::class);

        $returnedValue = $binaryFile->convertFieldValueToForm($binaryFileValue);

        $this->assertNull($returnedValue);
    }

    public function testConvertFieldValueFromForm()
    {
        $binaryFile = new BinaryFile();
        $data = $this->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getRealPath', 'getClientOriginalName', 'getSize'))
            ->getMock();

        $data->expects($this->once())
            ->willReturn('/some/path')
            ->method('getRealPath');

        $data->expects($this->once())
            ->willReturn('file.txt')
            ->method('getClientOriginalName');

        $data->expects($this->once())
            ->willReturn(123)
            ->method('getSize');

        $returnedData = $binaryFile->convertFieldValueFromForm($data);

        $this->assertInstanceOf(FileValue::class, $returnedData);
    }

    public function testConvertFieldValueFromFormWhenDataIsNull()
    {
        $binaryFile = new BinaryFile();

        $returnedData = $binaryFile->convertFieldValueFromForm(null);

        $this->assertNull($returnedData);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->exactly(2))
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

        $binaryFile = new BinaryFile();
        $binaryFile->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
