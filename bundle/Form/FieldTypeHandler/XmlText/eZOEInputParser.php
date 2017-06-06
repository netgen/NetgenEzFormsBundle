<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\XmlText;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\XmlText\Legacy\eZOELegacyInputParser;

class eZOEInputParser implements InputParser
{
    /**
     * @var callable
     */
    private $legacyKernelClosure;

    public function __construct(\Closure $legacyKernelClosure)
    {
        $this->legacyKernelClosure = $legacyKernelClosure;
    }

    /**
     * @param int $validateErrorLevel
     * @param int $detectErrorLevel
     * @param bool $parseLineBreaks
     * @param bool $removeDefaultAttrs
     *
     * @return \eZOEInputParser
     */
    public function getInputParser( $validateErrorLevel = \eZXMLInputParser::ERROR_NONE, $detectErrorLevel = \eZXMLInputParser::ERROR_NONE, $parseLineBreaks = false, $removeDefaultAttrs = false )
    {
        $kernelClosure = $this->legacyKernelClosure;

        return $kernelClosure()->runCallback(
            function () use ( $validateErrorLevel, $detectErrorLevel,$parseLineBreaks,$removeDefaultAttrs )
            {
                return new eZOELegacyInputParser( $validateErrorLevel, $detectErrorLevel, $parseLineBreaks, $removeDefaultAttrs );
            }
        );
    }
}
