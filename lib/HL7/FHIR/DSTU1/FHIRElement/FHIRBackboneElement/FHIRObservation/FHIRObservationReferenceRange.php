<?php

namespace HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRObservation;

/*!
 * This class was generated with the PHPFHIR library (https://github.com/dcarbone/php-fhir) using
 * class definitions from HL7 FHIR (https://www.hl7.org/fhir/)
 * 
 * Class creation date: July 8th, 2021 16:18+0000
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
 *   Copyright (c) 2011-2013, HL7, Inc.
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
 *   Generated on Tue, Sep 30, 2014 18:08+1000 for FHIR v0.0.82
 */

use HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement;
use HL7\FHIR\DSTU1\FHIRElement\FHIRCodeableConcept;
use HL7\FHIR\DSTU1\FHIRElement\FHIRExtension;
use HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity;
use HL7\FHIR\DSTU1\FHIRElement\FHIRRange;
use HL7\FHIR\DSTU1\FHIRIdPrimitive;
use HL7\FHIR\DSTU1\PHPFHIRConstants;
use HL7\FHIR\DSTU1\PHPFHIRTypeInterface;

/**
 * Measurements and simple assertions made about a patient, device or other
 * subject.
 *
 * Class FHIRObservationReferenceRange
 * @package \HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRObservation
 */
class FHIRObservationReferenceRange extends FHIRBackboneElement
{
    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_OBSERVATION_DOT_REFERENCE_RANGE;
    const FIELD_LOW = 'low';
    const FIELD_HIGH = 'high';
    const FIELD_MEANING = 'meaning';
    const FIELD_AGE = 'age';

    /** @var string */
    private $_xmlns = '';

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the low bound of the reference range. If this is omitted, the low
     * bound of the reference range is assumed to be meaningless. E.g. <2.3.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    protected $low = null;

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the high bound of the reference range. If this is omitted, the high
     * bound of the reference range is assumed to be meaningless. E.g. >5.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    protected $high = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Code for the meaning of the reference range.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRCodeableConcept
     */
    protected $meaning = null;

    /**
     * A set of ordered Quantities defined by a low and high limit.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The age at which this reference range is applicable. This is a neonatal age
     * (e.g. number of weeks at term) if the meaning says so.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRRange
     */
    protected $age = null;

    /**
     * Validation map for fields in type Observation.ReferenceRange
     * @var array
     */
    private static $_validationRules = [    ];

    /**
     * FHIRObservationReferenceRange Constructor
     * @param null|array $data
     */
    public function __construct($data = null)
    {
        if (null === $data || [] === $data) {
            return;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'FHIRObservationReferenceRange::_construct - $data expected to be null or array, %s seen',
                gettype($data)
            ));
        }
        parent::__construct($data);
        if (isset($data[self::FIELD_LOW])) {
            if ($data[self::FIELD_LOW] instanceof FHIRQuantity) {
                $this->setLow($data[self::FIELD_LOW]);
            } else {
                $this->setLow(new FHIRQuantity($data[self::FIELD_LOW]));
            }
        }
        if (isset($data[self::FIELD_HIGH])) {
            if ($data[self::FIELD_HIGH] instanceof FHIRQuantity) {
                $this->setHigh($data[self::FIELD_HIGH]);
            } else {
                $this->setHigh(new FHIRQuantity($data[self::FIELD_HIGH]));
            }
        }
        if (isset($data[self::FIELD_MEANING])) {
            if ($data[self::FIELD_MEANING] instanceof FHIRCodeableConcept) {
                $this->setMeaning($data[self::FIELD_MEANING]);
            } else {
                $this->setMeaning(new FHIRCodeableConcept($data[self::FIELD_MEANING]));
            }
        }
        if (isset($data[self::FIELD_AGE])) {
            if ($data[self::FIELD_AGE] instanceof FHIRRange) {
                $this->setAge($data[self::FIELD_AGE]);
            } else {
                $this->setAge(new FHIRRange($data[self::FIELD_AGE]));
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
        return "<ObservationReferenceRange{$xmlns}></ObservationReferenceRange>";
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the low bound of the reference range. If this is omitted, the low
     * bound of the reference range is assumed to be meaningless. E.g. <2.3.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    public function getLow()
    {
        return $this->low;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the low bound of the reference range. If this is omitted, the low
     * bound of the reference range is assumed to be meaningless. E.g. <2.3.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity $low
     * @return static
     */
    public function setLow(FHIRQuantity $low = null)
    {
        $this->_trackValueSet($this->low, $low);
        $this->low = $low;
        return $this;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the high bound of the reference range. If this is omitted, the high
     * bound of the reference range is assumed to be meaningless. E.g. >5.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    public function getHigh()
    {
        return $this->high;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The value of the high bound of the reference range. If this is omitted, the high
     * bound of the reference range is assumed to be meaningless. E.g. >5.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity $high
     * @return static
     */
    public function setHigh(FHIRQuantity $high = null)
    {
        $this->_trackValueSet($this->high, $high);
        $this->high = $high;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Code for the meaning of the reference range.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRCodeableConcept
     */
    public function getMeaning()
    {
        return $this->meaning;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Code for the meaning of the reference range.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRCodeableConcept $meaning
     * @return static
     */
    public function setMeaning(FHIRCodeableConcept $meaning = null)
    {
        $this->_trackValueSet($this->meaning, $meaning);
        $this->meaning = $meaning;
        return $this;
    }

    /**
     * A set of ordered Quantities defined by a low and high limit.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The age at which this reference range is applicable. This is a neonatal age
     * (e.g. number of weeks at term) if the meaning says so.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRRange
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * A set of ordered Quantities defined by a low and high limit.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The age at which this reference range is applicable. This is a neonatal age
     * (e.g. number of weeks at term) if the meaning says so.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRRange $age
     * @return static
     */
    public function setAge(FHIRRange $age = null)
    {
        $this->_trackValueSet($this->age, $age);
        $this->age = $age;
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
        if (null !== ($v = $this->getLow())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_LOW] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getHigh())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_HIGH] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getMeaning())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_MEANING] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getAge())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_AGE] = $fieldErrs;
            }
        }
        if (isset($validationRules[self::FIELD_LOW])) {
            $v = $this->getLow();
            foreach($validationRules[self::FIELD_LOW] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_OBSERVATION_DOT_REFERENCE_RANGE, self::FIELD_LOW, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_LOW])) {
                        $errs[self::FIELD_LOW] = [];
                    }
                    $errs[self::FIELD_LOW][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_HIGH])) {
            $v = $this->getHigh();
            foreach($validationRules[self::FIELD_HIGH] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_OBSERVATION_DOT_REFERENCE_RANGE, self::FIELD_HIGH, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_HIGH])) {
                        $errs[self::FIELD_HIGH] = [];
                    }
                    $errs[self::FIELD_HIGH][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_MEANING])) {
            $v = $this->getMeaning();
            foreach($validationRules[self::FIELD_MEANING] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_OBSERVATION_DOT_REFERENCE_RANGE, self::FIELD_MEANING, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_MEANING])) {
                        $errs[self::FIELD_MEANING] = [];
                    }
                    $errs[self::FIELD_MEANING][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_AGE])) {
            $v = $this->getAge();
            foreach($validationRules[self::FIELD_AGE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_OBSERVATION_DOT_REFERENCE_RANGE, self::FIELD_AGE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_AGE])) {
                        $errs[self::FIELD_AGE] = [];
                    }
                    $errs[self::FIELD_AGE][$rule] = $err;
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
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRObservation\FHIRObservationReferenceRange $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRObservation\FHIRObservationReferenceRange
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
                throw new \DomainException(sprintf('FHIRObservationReferenceRange::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRObservationReferenceRange::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRObservationReferenceRange(null);
        } elseif (!is_object($type) || !($type instanceof FHIRObservationReferenceRange)) {
            throw new \RuntimeException(sprintf(
                'FHIRObservationReferenceRange::xmlUnserialize - $type must be instance of \HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRObservation\FHIRObservationReferenceRange or null, %s seen.',
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
            if (self::FIELD_LOW === $n->nodeName) {
                $type->setLow(FHIRQuantity::xmlUnserialize($n));
            } elseif (self::FIELD_HIGH === $n->nodeName) {
                $type->setHigh(FHIRQuantity::xmlUnserialize($n));
            } elseif (self::FIELD_MEANING === $n->nodeName) {
                $type->setMeaning(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_AGE === $n->nodeName) {
                $type->setAge(FHIRRange::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER_EXTENSION === $n->nodeName) {
                $type->addModifierExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_EXTENSION === $n->nodeName) {
                $type->addExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_ID === $n->nodeName) {
                $type->setId(FHIRIdPrimitive::xmlUnserialize($n));
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
        if (null !== ($v = $this->getLow())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_LOW);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getHigh())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_HIGH);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getMeaning())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_MEANING);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getAge())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_AGE);
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
        if (null !== ($v = $this->getLow())) {
            $a[self::FIELD_LOW] = $v;
        }
        if (null !== ($v = $this->getHigh())) {
            $a[self::FIELD_HIGH] = $v;
        }
        if (null !== ($v = $this->getMeaning())) {
            $a[self::FIELD_MEANING] = $v;
        }
        if (null !== ($v = $this->getAge())) {
            $a[self::FIELD_AGE] = $v;
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