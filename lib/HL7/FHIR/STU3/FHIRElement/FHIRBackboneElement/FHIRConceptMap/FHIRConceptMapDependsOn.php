<?php

namespace HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRConceptMap;

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

use HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement;
use HL7\FHIR\STU3\FHIRElement\FHIRExtension;
use HL7\FHIR\STU3\FHIRElement\FHIRString;
use HL7\FHIR\STU3\FHIRElement\FHIRUri;
use HL7\FHIR\STU3\FHIRStringPrimitive;
use HL7\FHIR\STU3\PHPFHIRConstants;
use HL7\FHIR\STU3\PHPFHIRTypeInterface;

/**
 * A statement of relationships from one set of concepts to one or more other
 * concepts - either code systems or data elements, or classes in class models.
 *
 * Class FHIRConceptMapDependsOn
 * @package \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRConceptMap
 */
class FHIRConceptMapDependsOn extends FHIRBackboneElement
{
    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_CONCEPT_MAP_DOT_DEPENDS_ON;
    const FIELD_PROPERTY = 'property';
    const FIELD_PROPERTY_EXT = '_property';
    const FIELD_SYSTEM = 'system';
    const FIELD_SYSTEM_EXT = '_system';
    const FIELD_CODE = 'code';
    const FIELD_CODE_EXT = '_code';
    const FIELD_DISPLAY = 'display';
    const FIELD_DISPLAY_EXT = '_display';

    /** @var string */
    private $_xmlns = '';

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A reference to an element that holds a coded value that corresponds to a code
     * system property. The idea is that the information model carries an element
     * somwhere that is labeled to correspond with a code system property.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRUri
     */
    protected $property = null;

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An absolute URI that identifies the code system of the dependency code (if the
     * source/dependency is a value set that crosses code systems).
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRUri
     */
    protected $system = null;

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Identity (code or path) or the element/item/ValueSet that the map depends on /
     * refers to.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    protected $code = null;

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The display for the code. The display is only provided to help editors when
     * editing the concept map.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    protected $display = null;

    /**
     * Validation map for fields in type ConceptMap.DependsOn
     * @var array
     */
    private static $_validationRules = [    ];

    /**
     * FHIRConceptMapDependsOn Constructor
     * @param null|array $data
     */
    public function __construct($data = null)
    {
        if (null === $data || [] === $data) {
            return;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'FHIRConceptMapDependsOn::_construct - $data expected to be null or array, %s seen',
                gettype($data)
            ));
        }
        parent::__construct($data);
        if (isset($data[self::FIELD_PROPERTY]) || isset($data[self::FIELD_PROPERTY_EXT])) {
            $value = isset($data[self::FIELD_PROPERTY]) ? $data[self::FIELD_PROPERTY] : null;
            $ext = (isset($data[self::FIELD_PROPERTY_EXT]) && is_array($data[self::FIELD_PROPERTY_EXT])) ? $ext = $data[self::FIELD_PROPERTY_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRUri) {
                    $this->setProperty($value);
                } else if (is_array($value)) {
                    $this->setProperty(new FHIRUri(array_merge($ext, $value)));
                } else {
                    $this->setProperty(new FHIRUri([FHIRUri::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setProperty(new FHIRUri($ext));
            }
        }
        if (isset($data[self::FIELD_SYSTEM]) || isset($data[self::FIELD_SYSTEM_EXT])) {
            $value = isset($data[self::FIELD_SYSTEM]) ? $data[self::FIELD_SYSTEM] : null;
            $ext = (isset($data[self::FIELD_SYSTEM_EXT]) && is_array($data[self::FIELD_SYSTEM_EXT])) ? $ext = $data[self::FIELD_SYSTEM_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRUri) {
                    $this->setSystem($value);
                } else if (is_array($value)) {
                    $this->setSystem(new FHIRUri(array_merge($ext, $value)));
                } else {
                    $this->setSystem(new FHIRUri([FHIRUri::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setSystem(new FHIRUri($ext));
            }
        }
        if (isset($data[self::FIELD_CODE]) || isset($data[self::FIELD_CODE_EXT])) {
            $value = isset($data[self::FIELD_CODE]) ? $data[self::FIELD_CODE] : null;
            $ext = (isset($data[self::FIELD_CODE_EXT]) && is_array($data[self::FIELD_CODE_EXT])) ? $ext = $data[self::FIELD_CODE_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRString) {
                    $this->setCode($value);
                } else if (is_array($value)) {
                    $this->setCode(new FHIRString(array_merge($ext, $value)));
                } else {
                    $this->setCode(new FHIRString([FHIRString::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setCode(new FHIRString($ext));
            }
        }
        if (isset($data[self::FIELD_DISPLAY]) || isset($data[self::FIELD_DISPLAY_EXT])) {
            $value = isset($data[self::FIELD_DISPLAY]) ? $data[self::FIELD_DISPLAY] : null;
            $ext = (isset($data[self::FIELD_DISPLAY_EXT]) && is_array($data[self::FIELD_DISPLAY_EXT])) ? $ext = $data[self::FIELD_DISPLAY_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRString) {
                    $this->setDisplay($value);
                } else if (is_array($value)) {
                    $this->setDisplay(new FHIRString(array_merge($ext, $value)));
                } else {
                    $this->setDisplay(new FHIRString([FHIRString::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setDisplay(new FHIRString($ext));
            }
        }
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
    public function _getFHIRXMLElementDefinition()
    {
        $xmlns = $this->_getFHIRXMLNamespace();
        if ('' !==  $xmlns) {
            $xmlns = " xmlns=\"{$xmlns}\"";
        }
        return "<ConceptMapDependsOn{$xmlns}></ConceptMapDependsOn>";
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A reference to an element that holds a coded value that corresponds to a code
     * system property. The idea is that the information model carries an element
     * somwhere that is labeled to correspond with a code system property.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRUri
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A reference to an element that holds a coded value that corresponds to a code
     * system property. The idea is that the information model carries an element
     * somwhere that is labeled to correspond with a code system property.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRUri $property
     * @return static
     */
    public function setProperty($property = null)
    {
        if (null !== $property && !($property instanceof FHIRUri)) {
            $property = new FHIRUri($property);
        }
        $this->_trackValueSet($this->property, $property);
        $this->property = $property;
        return $this;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An absolute URI that identifies the code system of the dependency code (if the
     * source/dependency is a value set that crosses code systems).
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRUri
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An absolute URI that identifies the code system of the dependency code (if the
     * source/dependency is a value set that crosses code systems).
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRUri $system
     * @return static
     */
    public function setSystem($system = null)
    {
        if (null !== $system && !($system instanceof FHIRUri)) {
            $system = new FHIRUri($system);
        }
        $this->_trackValueSet($this->system, $system);
        $this->system = $system;
        return $this;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Identity (code or path) or the element/item/ValueSet that the map depends on /
     * refers to.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Identity (code or path) or the element/item/ValueSet that the map depends on /
     * refers to.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRString $code
     * @return static
     */
    public function setCode($code = null)
    {
        if (null !== $code && !($code instanceof FHIRString)) {
            $code = new FHIRString($code);
        }
        $this->_trackValueSet($this->code, $code);
        $this->code = $code;
        return $this;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The display for the code. The display is only provided to help editors when
     * editing the concept map.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The display for the code. The display is only provided to help editors when
     * editing the concept map.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRString $display
     * @return static
     */
    public function setDisplay($display = null)
    {
        if (null !== $display && !($display instanceof FHIRString)) {
            $display = new FHIRString($display);
        }
        $this->_trackValueSet($this->display, $display);
        $this->display = $display;
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
        $errs = parent::_getValidationErrors();
        $validationRules = $this->_getValidationRules();
        if (null !== ($v = $this->getProperty())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_PROPERTY] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getSystem())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_SYSTEM] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getCode())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_CODE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getDisplay())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_DISPLAY] = $fieldErrs;
            }
        }
        if (isset($validationRules[self::FIELD_PROPERTY])) {
            $v = $this->getProperty();
            foreach($validationRules[self::FIELD_PROPERTY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_CONCEPT_MAP_DOT_DEPENDS_ON, self::FIELD_PROPERTY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_PROPERTY])) {
                        $errs[self::FIELD_PROPERTY] = [];
                    }
                    $errs[self::FIELD_PROPERTY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SYSTEM])) {
            $v = $this->getSystem();
            foreach($validationRules[self::FIELD_SYSTEM] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_CONCEPT_MAP_DOT_DEPENDS_ON, self::FIELD_SYSTEM, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SYSTEM])) {
                        $errs[self::FIELD_SYSTEM] = [];
                    }
                    $errs[self::FIELD_SYSTEM][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_CODE])) {
            $v = $this->getCode();
            foreach($validationRules[self::FIELD_CODE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_CONCEPT_MAP_DOT_DEPENDS_ON, self::FIELD_CODE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_CODE])) {
                        $errs[self::FIELD_CODE] = [];
                    }
                    $errs[self::FIELD_CODE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_DISPLAY])) {
            $v = $this->getDisplay();
            foreach($validationRules[self::FIELD_DISPLAY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_CONCEPT_MAP_DOT_DEPENDS_ON, self::FIELD_DISPLAY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_DISPLAY])) {
                        $errs[self::FIELD_DISPLAY] = [];
                    }
                    $errs[self::FIELD_DISPLAY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_MODIFIER_EXTENSION])) {
            $v = $this->getModifierExtension();
            foreach($validationRules[self::FIELD_MODIFIER_EXTENSION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_BACKBONE_ELEMENT, self::FIELD_MODIFIER_EXTENSION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_MODIFIER_EXTENSION])) {
                        $errs[self::FIELD_MODIFIER_EXTENSION] = [];
                    }
                    $errs[self::FIELD_MODIFIER_EXTENSION][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_EXTENSION])) {
            $v = $this->getExtension();
            foreach($validationRules[self::FIELD_EXTENSION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_ELEMENT, self::FIELD_EXTENSION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_EXTENSION])) {
                        $errs[self::FIELD_EXTENSION] = [];
                    }
                    $errs[self::FIELD_EXTENSION][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_ID])) {
            $v = $this->getId();
            foreach($validationRules[self::FIELD_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_ELEMENT, self::FIELD_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_ID])) {
                        $errs[self::FIELD_ID] = [];
                    }
                    $errs[self::FIELD_ID][$rule] = $err;
                }
            }
        }
        return $errs;
    }

    /**
     * @param null|string|\DOMElement $element
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRConceptMap\FHIRConceptMapDependsOn $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRConceptMap\FHIRConceptMapDependsOn
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
                throw new \DomainException(sprintf('FHIRConceptMapDependsOn::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRConceptMapDependsOn::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRConceptMapDependsOn(null);
        } elseif (!is_object($type) || !($type instanceof FHIRConceptMapDependsOn)) {
            throw new \RuntimeException(sprintf(
                'FHIRConceptMapDependsOn::xmlUnserialize - $type must be instance of \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRConceptMap\FHIRConceptMapDependsOn or null, %s seen.',
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
            if (self::FIELD_PROPERTY === $n->nodeName) {
                $type->setProperty(FHIRUri::xmlUnserialize($n));
            } elseif (self::FIELD_SYSTEM === $n->nodeName) {
                $type->setSystem(FHIRUri::xmlUnserialize($n));
            } elseif (self::FIELD_CODE === $n->nodeName) {
                $type->setCode(FHIRString::xmlUnserialize($n));
            } elseif (self::FIELD_DISPLAY === $n->nodeName) {
                $type->setDisplay(FHIRString::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER_EXTENSION === $n->nodeName) {
                $type->addModifierExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_EXTENSION === $n->nodeName) {
                $type->addExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_ID === $n->nodeName) {
                $type->setId(FHIRStringPrimitive::xmlUnserialize($n));
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_PROPERTY);
        if (null !== $n) {
            $pt = $type->getProperty();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setProperty($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_SYSTEM);
        if (null !== $n) {
            $pt = $type->getSystem();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setSystem($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_CODE);
        if (null !== $n) {
            $pt = $type->getCode();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setCode($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_DISPLAY);
        if (null !== $n) {
            $pt = $type->getDisplay();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setDisplay($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_ID);
        if (null !== $n) {
            $pt = $type->getId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setId($n->nodeValue);
            }
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
        parent::xmlSerialize($element);
        if (null !== ($v = $this->getProperty())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_PROPERTY);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getSystem())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_SYSTEM);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getCode())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_CODE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getDisplay())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_DISPLAY);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        return $element;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $a = parent::jsonSerialize();
        if (null !== ($v = $this->getProperty())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_PROPERTY] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRUri::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_PROPERTY_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getSystem())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_SYSTEM] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRUri::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_SYSTEM_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getCode())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_CODE] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRString::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_CODE_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getDisplay())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_DISPLAY] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRString::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_DISPLAY_EXT] = $ext;
            }
        }
        return $a;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return self::FHIR_TYPE_NAME;
    }
}