<?php

namespace HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRMedicationPrescription;

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
use HL7\FHIR\DSTU1\FHIRElement\FHIRExtension;
use HL7\FHIR\DSTU1\FHIRElement\FHIRInteger;
use HL7\FHIR\DSTU1\FHIRElement\FHIRPeriod;
use HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity;
use HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity\FHIRDuration;
use HL7\FHIR\DSTU1\FHIRElement\FHIRResourceReference;
use HL7\FHIR\DSTU1\FHIRIdPrimitive;
use HL7\FHIR\DSTU1\PHPFHIRConstants;
use HL7\FHIR\DSTU1\PHPFHIRTypeInterface;

/**
 * An order for both supply of the medication and the instructions for
 * administration of the medicine to a patient.
 *
 * Class FHIRMedicationPrescriptionDispense
 * @package \HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRMedicationPrescription
 */
class FHIRMedicationPrescriptionDispense extends FHIRBackboneElement
{
    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE;
    const FIELD_MEDICATION = 'medication';
    const FIELD_VALIDITY_PERIOD = 'validityPeriod';
    const FIELD_NUMBER_OF_REPEATS_ALLOWED = 'numberOfRepeatsAllowed';
    const FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT = '_numberOfRepeatsAllowed';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_EXPECTED_SUPPLY_DURATION = 'expectedSupplyDuration';

    /** @var string */
    private $_xmlns = '';

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Identifies the medication that is to be dispensed. This may be a more
     * specifically defined than the medicationPrescription.medication . This is either
     * a link to a resource representing the details of the medication or a simple
     * attribute carrying a code that identifies the medication from a known list of
     * medications.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRResourceReference
     */
    protected $medication = null;

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Design Comments: This indicates the validity period of a prescription (stale
     * dating the Prescription) It reflects the prescriber perspective for the validity
     * of the prescription. Dispenses must not be made against the prescription outside
     * of this period. The lower-bound of the Dispensing Window signifies the earliest
     * date that the prescription can be filled for the first time. If an upper-bound
     * is not specified then the Prescription is open-ended or will default to a
     * stale-date based on regulations. Rationale: Indicates when the Prescription
     * becomes valid, and when it ceases to be a dispensable Prescription.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRPeriod
     */
    protected $validityPeriod = null;

    /**
     * A whole number
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An integer indicating the number of repeats of the Dispense. UsageNotes: For
     * example, the number of times the prescribed quantity is to be supplied including
     * the initial standard fill.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRInteger
     */
    protected $numberOfRepeatsAllowed = null;

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The amount that is to be dispensed.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    protected $quantity = null;

    /**
     * Identifies the period time over which the supplied product is expected to be
     * used, or the length of time the dispense is expected to last. In some
     * situations, this attribute may be used instead of quantity to identify the
     * amount supplied by how long it is expected to last, rather than the physical
     * quantity issued, e.g. 90 days supply of medication (based on an ordered dosage)
     * When possible, it is always better to specify quantity, as this tends to be more
     * precise. expectedSupplyDuration will always be an estimate that can be
     * influenced by external factors.
     *
     * @var null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity\FHIRDuration
     */
    protected $expectedSupplyDuration = null;

    /**
     * Validation map for fields in type MedicationPrescription.Dispense
     * @var array
     */
    private static $_validationRules = [    ];

    /**
     * FHIRMedicationPrescriptionDispense Constructor
     * @param null|array $data
     */
    public function __construct($data = null)
    {
        if (null === $data || [] === $data) {
            return;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'FHIRMedicationPrescriptionDispense::_construct - $data expected to be null or array, %s seen',
                gettype($data)
            ));
        }
        parent::__construct($data);
        if (isset($data[self::FIELD_MEDICATION])) {
            if ($data[self::FIELD_MEDICATION] instanceof FHIRResourceReference) {
                $this->setMedication($data[self::FIELD_MEDICATION]);
            } else {
                $this->setMedication(new FHIRResourceReference($data[self::FIELD_MEDICATION]));
            }
        }
        if (isset($data[self::FIELD_VALIDITY_PERIOD])) {
            if ($data[self::FIELD_VALIDITY_PERIOD] instanceof FHIRPeriod) {
                $this->setValidityPeriod($data[self::FIELD_VALIDITY_PERIOD]);
            } else {
                $this->setValidityPeriod(new FHIRPeriod($data[self::FIELD_VALIDITY_PERIOD]));
            }
        }
        if (isset($data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED]) || isset($data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT])) {
            $value = isset($data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED]) ? $data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED] : null;
            $ext = (isset($data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT]) && is_array($data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT])) ? $ext = $data[self::FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRInteger) {
                    $this->setNumberOfRepeatsAllowed($value);
                } else if (is_array($value)) {
                    $this->setNumberOfRepeatsAllowed(new FHIRInteger(array_merge($ext, $value)));
                } else {
                    $this->setNumberOfRepeatsAllowed(new FHIRInteger([FHIRInteger::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setNumberOfRepeatsAllowed(new FHIRInteger($ext));
            }
        }
        if (isset($data[self::FIELD_QUANTITY])) {
            if ($data[self::FIELD_QUANTITY] instanceof FHIRQuantity) {
                $this->setQuantity($data[self::FIELD_QUANTITY]);
            } else {
                $this->setQuantity(new FHIRQuantity($data[self::FIELD_QUANTITY]));
            }
        }
        if (isset($data[self::FIELD_EXPECTED_SUPPLY_DURATION])) {
            if ($data[self::FIELD_EXPECTED_SUPPLY_DURATION] instanceof FHIRDuration) {
                $this->setExpectedSupplyDuration($data[self::FIELD_EXPECTED_SUPPLY_DURATION]);
            } else {
                $this->setExpectedSupplyDuration(new FHIRDuration($data[self::FIELD_EXPECTED_SUPPLY_DURATION]));
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
        return "<MedicationPrescriptionDispense{$xmlns}></MedicationPrescriptionDispense>";
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Identifies the medication that is to be dispensed. This may be a more
     * specifically defined than the medicationPrescription.medication . This is either
     * a link to a resource representing the details of the medication or a simple
     * attribute carrying a code that identifies the medication from a known list of
     * medications.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRResourceReference
     */
    public function getMedication()
    {
        return $this->medication;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Identifies the medication that is to be dispensed. This may be a more
     * specifically defined than the medicationPrescription.medication . This is either
     * a link to a resource representing the details of the medication or a simple
     * attribute carrying a code that identifies the medication from a known list of
     * medications.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRResourceReference $medication
     * @return static
     */
    public function setMedication(FHIRResourceReference $medication = null)
    {
        $this->_trackValueSet($this->medication, $medication);
        $this->medication = $medication;
        return $this;
    }

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Design Comments: This indicates the validity period of a prescription (stale
     * dating the Prescription) It reflects the prescriber perspective for the validity
     * of the prescription. Dispenses must not be made against the prescription outside
     * of this period. The lower-bound of the Dispensing Window signifies the earliest
     * date that the prescription can be filled for the first time. If an upper-bound
     * is not specified then the Prescription is open-ended or will default to a
     * stale-date based on regulations. Rationale: Indicates when the Prescription
     * becomes valid, and when it ceases to be a dispensable Prescription.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRPeriod
     */
    public function getValidityPeriod()
    {
        return $this->validityPeriod;
    }

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Design Comments: This indicates the validity period of a prescription (stale
     * dating the Prescription) It reflects the prescriber perspective for the validity
     * of the prescription. Dispenses must not be made against the prescription outside
     * of this period. The lower-bound of the Dispensing Window signifies the earliest
     * date that the prescription can be filled for the first time. If an upper-bound
     * is not specified then the Prescription is open-ended or will default to a
     * stale-date based on regulations. Rationale: Indicates when the Prescription
     * becomes valid, and when it ceases to be a dispensable Prescription.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRPeriod $validityPeriod
     * @return static
     */
    public function setValidityPeriod(FHIRPeriod $validityPeriod = null)
    {
        $this->_trackValueSet($this->validityPeriod, $validityPeriod);
        $this->validityPeriod = $validityPeriod;
        return $this;
    }

    /**
     * A whole number
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An integer indicating the number of repeats of the Dispense. UsageNotes: For
     * example, the number of times the prescribed quantity is to be supplied including
     * the initial standard fill.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRInteger
     */
    public function getNumberOfRepeatsAllowed()
    {
        return $this->numberOfRepeatsAllowed;
    }

    /**
     * A whole number
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * An integer indicating the number of repeats of the Dispense. UsageNotes: For
     * example, the number of times the prescribed quantity is to be supplied including
     * the initial standard fill.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRInteger $numberOfRepeatsAllowed
     * @return static
     */
    public function setNumberOfRepeatsAllowed($numberOfRepeatsAllowed = null)
    {
        if (null !== $numberOfRepeatsAllowed && !($numberOfRepeatsAllowed instanceof FHIRInteger)) {
            $numberOfRepeatsAllowed = new FHIRInteger($numberOfRepeatsAllowed);
        }
        $this->_trackValueSet($this->numberOfRepeatsAllowed, $numberOfRepeatsAllowed);
        $this->numberOfRepeatsAllowed = $numberOfRepeatsAllowed;
        return $this;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The amount that is to be dispensed.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The amount that is to be dispensed.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity $quantity
     * @return static
     */
    public function setQuantity(FHIRQuantity $quantity = null)
    {
        $this->_trackValueSet($this->quantity, $quantity);
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Identifies the period time over which the supplied product is expected to be
     * used, or the length of time the dispense is expected to last. In some
     * situations, this attribute may be used instead of quantity to identify the
     * amount supplied by how long it is expected to last, rather than the physical
     * quantity issued, e.g. 90 days supply of medication (based on an ordered dosage)
     * When possible, it is always better to specify quantity, as this tends to be more
     * precise. expectedSupplyDuration will always be an estimate that can be
     * influenced by external factors.
     *
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity\FHIRDuration
     */
    public function getExpectedSupplyDuration()
    {
        return $this->expectedSupplyDuration;
    }

    /**
     * Identifies the period time over which the supplied product is expected to be
     * used, or the length of time the dispense is expected to last. In some
     * situations, this attribute may be used instead of quantity to identify the
     * amount supplied by how long it is expected to last, rather than the physical
     * quantity issued, e.g. 90 days supply of medication (based on an ordered dosage)
     * When possible, it is always better to specify quantity, as this tends to be more
     * precise. expectedSupplyDuration will always be an estimate that can be
     * influenced by external factors.
     *
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRQuantity\FHIRDuration $expectedSupplyDuration
     * @return static
     */
    public function setExpectedSupplyDuration(FHIRDuration $expectedSupplyDuration = null)
    {
        $this->_trackValueSet($this->expectedSupplyDuration, $expectedSupplyDuration);
        $this->expectedSupplyDuration = $expectedSupplyDuration;
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
        if (null !== ($v = $this->getMedication())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_MEDICATION] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getValidityPeriod())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_VALIDITY_PERIOD] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getNumberOfRepeatsAllowed())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_NUMBER_OF_REPEATS_ALLOWED] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getQuantity())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_QUANTITY] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getExpectedSupplyDuration())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_EXPECTED_SUPPLY_DURATION] = $fieldErrs;
            }
        }
        if (isset($validationRules[self::FIELD_MEDICATION])) {
            $v = $this->getMedication();
            foreach($validationRules[self::FIELD_MEDICATION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE, self::FIELD_MEDICATION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_MEDICATION])) {
                        $errs[self::FIELD_MEDICATION] = [];
                    }
                    $errs[self::FIELD_MEDICATION][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_VALIDITY_PERIOD])) {
            $v = $this->getValidityPeriod();
            foreach($validationRules[self::FIELD_VALIDITY_PERIOD] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE, self::FIELD_VALIDITY_PERIOD, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_VALIDITY_PERIOD])) {
                        $errs[self::FIELD_VALIDITY_PERIOD] = [];
                    }
                    $errs[self::FIELD_VALIDITY_PERIOD][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_NUMBER_OF_REPEATS_ALLOWED])) {
            $v = $this->getNumberOfRepeatsAllowed();
            foreach($validationRules[self::FIELD_NUMBER_OF_REPEATS_ALLOWED] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE, self::FIELD_NUMBER_OF_REPEATS_ALLOWED, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_NUMBER_OF_REPEATS_ALLOWED])) {
                        $errs[self::FIELD_NUMBER_OF_REPEATS_ALLOWED] = [];
                    }
                    $errs[self::FIELD_NUMBER_OF_REPEATS_ALLOWED][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_QUANTITY])) {
            $v = $this->getQuantity();
            foreach($validationRules[self::FIELD_QUANTITY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE, self::FIELD_QUANTITY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_QUANTITY])) {
                        $errs[self::FIELD_QUANTITY] = [];
                    }
                    $errs[self::FIELD_QUANTITY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_EXPECTED_SUPPLY_DURATION])) {
            $v = $this->getExpectedSupplyDuration();
            foreach($validationRules[self::FIELD_EXPECTED_SUPPLY_DURATION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_MEDICATION_PRESCRIPTION_DOT_DISPENSE, self::FIELD_EXPECTED_SUPPLY_DURATION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_EXPECTED_SUPPLY_DURATION])) {
                        $errs[self::FIELD_EXPECTED_SUPPLY_DURATION] = [];
                    }
                    $errs[self::FIELD_EXPECTED_SUPPLY_DURATION][$rule] = $err;
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
     * @param null|\HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRMedicationPrescription\FHIRMedicationPrescriptionDispense $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRMedicationPrescription\FHIRMedicationPrescriptionDispense
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
                throw new \DomainException(sprintf('FHIRMedicationPrescriptionDispense::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRMedicationPrescriptionDispense::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRMedicationPrescriptionDispense(null);
        } elseif (!is_object($type) || !($type instanceof FHIRMedicationPrescriptionDispense)) {
            throw new \RuntimeException(sprintf(
                'FHIRMedicationPrescriptionDispense::xmlUnserialize - $type must be instance of \HL7\FHIR\DSTU1\FHIRElement\FHIRBackboneElement\FHIRMedicationPrescription\FHIRMedicationPrescriptionDispense or null, %s seen.',
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
            if (self::FIELD_MEDICATION === $n->nodeName) {
                $type->setMedication(FHIRResourceReference::xmlUnserialize($n));
            } elseif (self::FIELD_VALIDITY_PERIOD === $n->nodeName) {
                $type->setValidityPeriod(FHIRPeriod::xmlUnserialize($n));
            } elseif (self::FIELD_NUMBER_OF_REPEATS_ALLOWED === $n->nodeName) {
                $type->setNumberOfRepeatsAllowed(FHIRInteger::xmlUnserialize($n));
            } elseif (self::FIELD_QUANTITY === $n->nodeName) {
                $type->setQuantity(FHIRQuantity::xmlUnserialize($n));
            } elseif (self::FIELD_EXPECTED_SUPPLY_DURATION === $n->nodeName) {
                $type->setExpectedSupplyDuration(FHIRDuration::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER_EXTENSION === $n->nodeName) {
                $type->addModifierExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_EXTENSION === $n->nodeName) {
                $type->addExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_ID === $n->nodeName) {
                $type->setId(FHIRIdPrimitive::xmlUnserialize($n));
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_NUMBER_OF_REPEATS_ALLOWED);
        if (null !== $n) {
            $pt = $type->getNumberOfRepeatsAllowed();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setNumberOfRepeatsAllowed($n->nodeValue);
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
        if (null !== ($v = $this->getMedication())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_MEDICATION);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getValidityPeriod())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_VALIDITY_PERIOD);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getNumberOfRepeatsAllowed())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_NUMBER_OF_REPEATS_ALLOWED);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getQuantity())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_QUANTITY);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getExpectedSupplyDuration())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_EXPECTED_SUPPLY_DURATION);
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
        if (null !== ($v = $this->getMedication())) {
            $a[self::FIELD_MEDICATION] = $v;
        }
        if (null !== ($v = $this->getValidityPeriod())) {
            $a[self::FIELD_VALIDITY_PERIOD] = $v;
        }
        if (null !== ($v = $this->getNumberOfRepeatsAllowed())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_NUMBER_OF_REPEATS_ALLOWED] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRInteger::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_NUMBER_OF_REPEATS_ALLOWED_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getQuantity())) {
            $a[self::FIELD_QUANTITY] = $v;
        }
        if (null !== ($v = $this->getExpectedSupplyDuration())) {
            $a[self::FIELD_EXPECTED_SUPPLY_DURATION] = $v;
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