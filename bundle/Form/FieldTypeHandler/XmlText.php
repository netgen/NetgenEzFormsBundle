<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use eZ\Publish\SPI\FieldType\Value;
use eZ\Publish\Core\FieldType\XmlText\Converter\Html5 as Html5Converter;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\XmlText\InputParser;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\XmlText\Value as XmlTextValue;
use eZ\Publish\Core\FieldType\XmlText\Input;
use eZ\Publish\Core\FieldType\XmlText\Type;

class XmlText extends FieldTypeHandler
{
    /**
     * @var Type
     */
    protected $type;

    /**
     * @var InputParser
     */
    protected $inputParser;

    /**
     * @var  Html5Converter
     */
    protected $xmlTextConverter;

    /**
     * XmlText constructor.
     *
     * @param Type $type
     */
    public function __construct(Type $type, InputParser $inputParser, Html5Converter $xmlTextConverter)
    {
        $this->type = $type;
        $this->inputParser = $inputParser;
        $this->xmlTextConverter = $xmlTextConverter;
    }
    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $options['block_name'] = 'ezforms_xml';

        $formBuilder->add($fieldDefinition->identifier, 'ezforms_xml', $options);
    }

    /**
     *
     *
     * @param Value $value
     * @param FieldDefinition $fieldDefinition
     *
     * @return array
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        if (!$value instanceof XmlTextValue) {
            return new \DOMDocument();
        }

        if ($this->type->isEmptyValue($value)) {
            return new \DOMDocument();
        }

        $xml = '';
        foreach ($value->xml->childNodes as $node) {
            $xml = $xml . $value->xml->saveXML($node);
        }

        $document = new \DOMDocument( '1.0', 'utf-8' );
        $success = $document->loadXML( $xml );

        if(!$success)
        {
            return '';
        }
        else
        {
            return $this->xmlTextConverter->convert($document);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return XmlTextValue
     */
    public function convertFieldValueFromForm($data)
    {
        $parser = $this->inputParser->getInputParser();
        $parser->setParseLineBreaks(true);

        $document = $parser->process(trim($data));

        $xml = new XmlTextValue($document);

        return $xml;
    }
}
