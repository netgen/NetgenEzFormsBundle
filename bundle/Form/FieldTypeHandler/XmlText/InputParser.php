<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\XmlText;

interface InputParser
{
    public function getInputParser( $validateErrorLevel = \eZXMLInputParser::ERROR_NONE, $detectErrorLevel = \eZXMLInputParser::ERROR_NONE, $parseLineBreaks = false, $removeDefaultAttrs = false );
}
