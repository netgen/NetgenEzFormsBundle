<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler\XmlText\Legacy;

use DOMElement;

class eZOELegacyInputParser extends \eZOEInputParser
{
    /**
     * structHandlerHeader (Structure handler, pass 2 after childre tags)
     * Structure handler for header tag.
     *
     * @param DOMElement $element
     * @param DOMElement $newParent node that are going to become new parent.
     * @return array changes structure if it contains 'result' key
     */
    function structHandlerHeader( $element, $newParent )
    {
        $ret = array();
        $parent = $element->parentNode;
        $level = $element->getAttribute( 'level' );
        if ( !$level )
        {
            $level = 1;
        }

        $element->removeAttribute( 'level' );
        if ( $level )
        {
            $sectionLevel = 0;
            $current = $element;
            while ( $current->parentNode )
            {
                $tmp = $current;
                $current = $tmp->parentNode;
                if ( $current->nodeName === 'section' )
                {
                    ++$sectionLevel;
                }
                elseif ( $current->nodeName === 'td' )
                {
                    ++$sectionLevel;
                    break;
                }
            }
            if ( $level > $sectionLevel )
            {
                $newTempParent = $parent;
                for ( $i = $sectionLevel; $i < $level; $i++ )
                {
                    $newSection = $this->Document->createElement( 'section' );
                    if ( $i == $sectionLevel )
                    {
                        $newSection = $newTempParent->insertBefore( $newSection, $element );
                    }
                    else
                    {
                        $newTempParent->appendChild( $newSection );
                    }
                    // Schema check
                    if ( !$this->processBySchemaTree( $newSection ) )
                    {
                        return $ret;
                    }
                    $newTempParent = $newSection;
                    unset( $newSection );
                }
                $elementToMove = $element;
                while( $elementToMove &&
                    $elementToMove->nodeName !== 'section' )
                {
                    $next = $elementToMove->nextSibling;
                    $elementToMove = $parent->removeChild( $elementToMove );
                    $newTempParent->appendChild( $elementToMove );
                    $elementToMove = $next;

                    if ( !$elementToMove ||
                        ( $elementToMove->nodeName === 'header' &&
                            $elementToMove->getAttribute( 'level' ) <= $level ) )
                        break;
                }
            }
            elseif ( $level < $sectionLevel )
            {
                $newLevel = $sectionLevel + 1;
                $current = $element;
                while( $level < $newLevel )
                {
                    $tmp = $current;
                    $current = $tmp->parentNode;
                    if ( $current->nodeName === 'section' )
                        --$newLevel;
                }
                $elementToMove = $element;
                while ( $elementToMove->parentNode->nodeName === 'custom' )
                {
                    $elementToMove = $elementToMove->parentNode;
                    $parent = $elementToMove->parentNode;
                }
                while( $elementToMove &&
                    $elementToMove->nodeName !== 'section' )
                {
                    $next = $elementToMove->nextSibling;
                    $parent->removeChild( $elementToMove );
                    $current->appendChild( $elementToMove );
                    $elementToMove = $next;

                    if ( !$elementToMove ||
                        ( $elementToMove->nodeName === 'header' &&
                            $elementToMove->getAttribute( 'level' ) <= $level ) )
                        break;
                }
            }
        }
        return $ret;
    }
}
