<?php

namespace HL7\FHIR\STU3;

/*!
 * This class was generated with the PHPFHIR library (https://github.com/dcarbone/php-fhir) using
 * class definitions from HL7 FHIR (https://www.hl7.org/fhir/)
 * 
 * Class creation date: July 8th, 2021 16:19+0000
 * 
 * PHPFHIR Copyright:
 * 
 * Copyright 2016-2021 Daniel Carbone (daniel.p.carbone@gmail.com)
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 *
 * FHIR Copyright Notice:
 *
 *   Copyright (c) 2011+, HL7, Inc.
 *   All rights reserved.
 * 
 *   Redistribution and use in source and binary forms, with or without modification,
 *   are permitted provided that the following conditions are met:
 * 
 *    * Redistributions of source code must retain the above copyright notice, this
 *      list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright notice,
 *      this list of conditions and the following disclaimer in the documentation
 *      and/or other materials provided with the distribution.
 *    * Neither the name of HL7 nor the names of its contributors may be used to
 *      endorse or promote products derived from this software without specific
 *      prior written permission.
 * 
 *   THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *   ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *   WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 *   IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 *   INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 *   NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 *   PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 *   WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *   ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *   POSSIBILITY OF SUCH DAMAGE.
 * 
 * 
 *   Generated on Wed, Apr 19, 2017 07:44+1000 for FHIR v3.0.1
 * 
 *   Note: the schemas & schematrons do not contain all of the rules about what makes resources
 *   valid. Implementers will still need to be familiar with the content of the specification and with
 *   any profiles that apply to the resources in order to make a conformant implementation.
 * 
 */

/**
 * Class FHIRPositiveIntPrimitive
 * @package \HL7\FHIR\STU3
 */
class FHIRPositiveIntPrimitive implements PHPFHIRTypeInterface
{
    use PHPFHIRValidationAssertionsTrait;
    use PHPFHIRChangeTrackingTrait;

    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_POSITIVE_INT_HYPHEN_PRIMITIVE;
    const FIELD_VALUE = 'value';

    /** @var string */
    private $_xmlns = '';

    /**
     * @var null|integer
     */
    protected $value = null;

    /**
     * Validation map for fields in type positiveInt-primitive
     * @var array
     */
    private static $_validationRules = [
        self::FIELD_VALUE => [
            PHPFHIRConstants::VALIDATE_PATTERN => '/^[1-9][0-9]*$/',
        ],
    ];

    /**
     * FHIRPositiveIntPrimitive Constructor
     * @param null|integer $value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function _getFHIRTypeName()
    {
        return self::FHIR_TYPE_NAME;
    }

    /**
     * @return string
     */
    public function _getFHIRXMLNamespace()
    {
        return $this->_xmlns;
    }

    /**
     * @param null|string $xmlNamespace
     * @return static
     */
    public function _setFHIRXMLNamespace($xmlNamespace)
    {
        $this->_xmlns = trim((string)$xmlNamespace);
        return $this;
    }


    /**
     * @return string
     */
    public function _getFHIRXMLElementDefinition()
    {
        $xmlns = $this->_getFHIRXMLNamespace();
        if ('' !==  $xmlns) {
            $xmlns = " xmlns=\"{$xmlns}\"";
        }
        return "<positiveInt_primitive{$xmlns}></positiveInt_primitive>";
    }

    /**
     * @return null|integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|integer|string $value
     * @return static
     */
    public function setValue($value)
    {
        if (null === $value) {
            $this->value = null;
            return $this;
        }
        if (is_string($value)) {
            if ('' === $value) {
                $value = 0;
            } else {
                $neg = 1;
                if ('-' === $value[0]) {
                    $neg = -1;
                    $value = substr($value, 1);
                }
                $value = $neg * intval($value, 10);
            }
        }
        if (!is_int($value)) {
            throw new \InvalidArgumentException(sprintf('Value must be null, integer, or string containing only numbers, %s seen.', $value));
        }
        $this->value = $value;
        return $this;
    }

    /**
     * Returns the validation rules that this type's fields must comply with to be considered "valid"
     * The returned array is in ["fieldname[.offset]" => ["rule" => {constraint}]]
     *
     * @return array
     */
    public function _getValidationRules()
    {
        return self::$_validationRules;
    }

    /**
     * Validates that this type conforms to the specifications set forth for it by FHIR.  An empty array must be seen as
     * passing.
     *
     * @return array
     */
    public function _getValidationErrors()
    {
        $errs = [];
        $validationRules = $this->_getValidationRules();
        if (isset($validationRules[self::FIELD_VALUE]) && null !== ($v = $this->getValue())) {
            foreach($validationRules[self::FIELD_VALUE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_POSITIVE_INT_HYPHEN_PRIMITIVE, self::FIELD_VALUE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_VALUE])) {
                        $errs[self::FIELD_VALUE] = [];
                    }
                    $errs[self::FIELD_VALUE][$rule] = $err;
                }
            }
        }
        return $errs;
    }

    /**
     * @param null|string|\DOMElement $element
     * @param null|\HL7\FHIR\STU3\FHIRPositiveIntPrimitive $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\STU3\FHIRPositiveIntPrimitive
     */
    public static function xmlUnserialize($element = null, PHPFHIRTypeInterface $type = null, $libxmlOpts = 591872)
    {
        if (null === $element) {
            return null;
        }
        if (is_string($element)) {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadXML($element, $libxmlOpts);
            if (false === $dom) {
                throw new \DomainException(sprintf('FHIRPositiveIntPrimitive::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRPositiveIntPrimitive::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRPositiveIntPrimitive(null);
        } elseif (!is_object($type) || !($type instanceof FHIRPositiveIntPrimitive)) {
            throw new \RuntimeException(sprintf(
                'FHIRPositiveIntPrimitive::xmlUnserialize - $type must be instance of \HL7\FHIR\STU3\FHIRPositiveIntPrimitive or null, %s seen.',
                is_object($type) ? get_class($type) : gettype($type)
            ));
        }
        if ('' === $type->_getFHIRXMLNamespace() && (null === $element->parentNode || $element->namespaceURI !== $element->parentNode->namespaceURI)) {
            $type->_setFHIRXMLNamespace($element->namespaceURI);
        }
        for($i = 0; $i < $element->childNodes->length; $i++) {
            $n = $element->childNodes->item($i);
            if (!($n instanceof \DOMElement)) {
                continue;
            }
            if (self::FIELD_VALUE === $n->nodeName) {
                $valueAttr = $n->attributes->getNamedItem('value');
                if (null !== $valueAttr) {
                    $type->setValue($valueAttr->nodeValue);
                } elseif ($n->hasChildNodes()) {
                    $type->setValue($n->ownerDocument->saveXML($n));
                } else {
                    $type->setValue($n->textContent);
                }
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_VALUE);
        if (null !== $n) {
            $type->setValue($n->nodeValue);
        }
        return $type;
    }

    /**
     * @param null|\DOMElement $element
     * @param null|int $libxmlOpts
     * @return \DOMElement
     */
    public function xmlSerialize(\DOMElement $element = null, $libxmlOpts = 591872)
    {
        if (null === $element) {
            $dom = new \DOMDocument();
            $dom->loadXML($this->_getFHIRXMLElementDefinition(), $libxmlOpts);
            $element = $dom->documentElement;
        } elseif (null === $element->namespaceURI && '' !== ($xmlns = $this->_getFHIRXMLNamespace())) {
            $element->setAttribute('xmlns', $xmlns);
        }
        $element->setAttribute(self::FIELD_VALUE, (string)$this);
        return $element;
    }

    /**
     * @return null|integer
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }
}