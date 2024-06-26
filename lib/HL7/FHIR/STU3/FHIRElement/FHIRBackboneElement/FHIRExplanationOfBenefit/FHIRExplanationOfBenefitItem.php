<?php

namespace HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit;

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

use HL7\FHIR\STU3\FHIRElement\FHIRAddress;
use HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement;
use HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept;
use HL7\FHIR\STU3\FHIRElement\FHIRDate;
use HL7\FHIR\STU3\FHIRElement\FHIRDecimal;
use HL7\FHIR\STU3\FHIRElement\FHIRExtension;
use HL7\FHIR\STU3\FHIRElement\FHIRPeriod;
use HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt;
use HL7\FHIR\STU3\FHIRElement\FHIRQuantity;
use HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney;
use HL7\FHIR\STU3\FHIRElement\FHIRReference;
use HL7\FHIR\STU3\FHIRStringPrimitive;
use HL7\FHIR\STU3\PHPFHIRConstants;
use HL7\FHIR\STU3\PHPFHIRTypeInterface;

/**
 * This resource provides: the claim details; adjudication details from the
 * processing of a Claim; and optionally account balance information, for informing
 * the subscriber of the benefits provided.
 *
 * Class FHIRExplanationOfBenefitItem
 * @package \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit
 */
class FHIRExplanationOfBenefitItem extends FHIRBackboneElement
{
    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM;
    const FIELD_SEQUENCE = 'sequence';
    const FIELD_SEQUENCE_EXT = '_sequence';
    const FIELD_CARE_TEAM_LINK_ID = 'careTeamLinkId';
    const FIELD_CARE_TEAM_LINK_ID_EXT = '_careTeamLinkId';
    const FIELD_DIAGNOSIS_LINK_ID = 'diagnosisLinkId';
    const FIELD_DIAGNOSIS_LINK_ID_EXT = '_diagnosisLinkId';
    const FIELD_PROCEDURE_LINK_ID = 'procedureLinkId';
    const FIELD_PROCEDURE_LINK_ID_EXT = '_procedureLinkId';
    const FIELD_INFORMATION_LINK_ID = 'informationLinkId';
    const FIELD_INFORMATION_LINK_ID_EXT = '_informationLinkId';
    const FIELD_REVENUE = 'revenue';
    const FIELD_CATEGORY = 'category';
    const FIELD_SERVICE = 'service';
    const FIELD_MODIFIER = 'modifier';
    const FIELD_PROGRAM_CODE = 'programCode';
    const FIELD_SERVICED_DATE = 'servicedDate';
    const FIELD_SERVICED_DATE_EXT = '_servicedDate';
    const FIELD_SERVICED_PERIOD = 'servicedPeriod';
    const FIELD_LOCATION_CODEABLE_CONCEPT = 'locationCodeableConcept';
    const FIELD_LOCATION_ADDRESS = 'locationAddress';
    const FIELD_LOCATION_REFERENCE = 'locationReference';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_UNIT_PRICE = 'unitPrice';
    const FIELD_FACTOR = 'factor';
    const FIELD_FACTOR_EXT = '_factor';
    const FIELD_NET = 'net';
    const FIELD_UDI = 'udi';
    const FIELD_BODY_SITE = 'bodySite';
    const FIELD_SUB_SITE = 'subSite';
    const FIELD_ENCOUNTER = 'encounter';
    const FIELD_NOTE_NUMBER = 'noteNumber';
    const FIELD_NOTE_NUMBER_EXT = '_noteNumber';
    const FIELD_ADJUDICATION = 'adjudication';
    const FIELD_DETAIL = 'detail';

    /** @var string */
    private $_xmlns = '';

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A service line number.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt
     */
    protected $sequence = null;

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Careteam applicable for this service or product line.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    protected $careTeamLinkId = [];

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Diagnosis applicable for this service or product line.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    protected $diagnosisLinkId = [];

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Procedures applicable for this service or product line.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    protected $procedureLinkId = [];

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Exceptions, special conditions and supporting information pplicable for this
     * service or product line.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    protected $informationLinkId = [];

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The type of reveneu or cost center providing the product and/or service.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    protected $revenue = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Health Care Service Type Codes to identify the classification of service or
     * benefits.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    protected $category = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If this is an actual service or product line, ie. not a Group, then use code to
     * indicate the Professional Service or Product supplied (eg. CTP,
     * HCPCS,USCLS,ICD10, NCPDP,DIN,ACHI,CCI). If a grouping item then use a group code
     * to indicate the type of thing being grouped eg. 'glasses' or 'compound'.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    protected $service = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Item typification or modifiers codes, eg for Oral whether the treatment is
     * cosmetic or associated with TMJ, or for medical whether the treatment was
     * outside the clinic or out of office hours.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    protected $modifier = [];

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * For programs which require reson codes for the inclusion, covering, of this
     * billed item under the program or sub-program.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    protected $programCode = [];

    /**
     * A date or partial date (e.g. just year or year + month). There is no time zone.
     * The format is a union of the schema types gYear, gYearMonth and date. Dates
     * SHALL be valid dates.
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRDate
     */
    protected $servicedDate = null;

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPeriod
     */
    protected $servicedPeriod = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    protected $locationCodeableConcept = null;

    /**
     * An address expressed using postal conventions (as opposed to GPS or other
     * location definition formats). This data type may be used to convey addresses for
     * use in delivering mail as well as for visiting locations which might not be
     * valid for mail delivery. There are a variety of postal address formats defined
     * around the world.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRAddress
     */
    protected $locationAddress = null;

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    protected $locationReference = null;

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The number of repetitions of a service or product.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity
     */
    protected $quantity = null;

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If the item is a node then this is the fee for the product or service, otherwise
     * this is the total of the fees for the children of the group.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney
     */
    protected $unitPrice = null;

    /**
     * A rational number with implicit precision
     * Do not use a IEEE type floating point type, instead use something that works
     * like a true decimal, with inbuilt precision (e.g. Java BigInteger)
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A real number that represents a multiplier used in determining the overall value
     * of services delivered and/or goods received. The concept of a Factor allows for
     * a discount or surcharge multiplier to be applied to a monetary amount.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRDecimal
     */
    protected $factor = null;

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The quantity times the unit price for an addittional service or product or
     * charge. For example, the formula: unit Quantity * unit Price (Cost per Point) *
     * factor Number * points = net Amount. Quantity, factor and points are assumed to
     * be 1 if not supplied.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney
     */
    protected $net = null;

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * List of Unique Device Identifiers associated with this line item.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRReference[]
     */
    protected $udi = [];

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Physical service site on the patient (limb, tooth, etc).
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    protected $bodySite = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A region or surface of the site, eg. limb region or tooth surface(s).
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    protected $subSite = [];

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A billed item may include goods or services provided in multiple encounters.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRReference[]
     */
    protected $encounter = [];

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A list of note references to the notes provided below.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    protected $noteNumber = [];

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * The adjudications results.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitAdjudication[]
     */
    protected $adjudication = [];

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * Second tier of goods and services.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitDetail[]
     */
    protected $detail = [];

    /**
     * Validation map for fields in type ExplanationOfBenefit.Item
     * @var array
     */
    private static $_validationRules = [    ];

    /**
     * FHIRExplanationOfBenefitItem Constructor
     * @param null|array $data
     */
    public function __construct($data = null)
    {
        if (null === $data || [] === $data) {
            return;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'FHIRExplanationOfBenefitItem::_construct - $data expected to be null or array, %s seen',
                gettype($data)
            ));
        }
        parent::__construct($data);
        if (isset($data[self::FIELD_SEQUENCE]) || isset($data[self::FIELD_SEQUENCE_EXT])) {
            $value = isset($data[self::FIELD_SEQUENCE]) ? $data[self::FIELD_SEQUENCE] : null;
            $ext = (isset($data[self::FIELD_SEQUENCE_EXT]) && is_array($data[self::FIELD_SEQUENCE_EXT])) ? $ext = $data[self::FIELD_SEQUENCE_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->setSequence($value);
                } else if (is_array($value)) {
                    $this->setSequence(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->setSequence(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setSequence(new FHIRPositiveInt($ext));
            }
        }
        if (isset($data[self::FIELD_CARE_TEAM_LINK_ID]) || isset($data[self::FIELD_CARE_TEAM_LINK_ID_EXT])) {
            $value = isset($data[self::FIELD_CARE_TEAM_LINK_ID]) ? $data[self::FIELD_CARE_TEAM_LINK_ID] : null;
            $ext = (isset($data[self::FIELD_CARE_TEAM_LINK_ID_EXT]) && is_array($data[self::FIELD_CARE_TEAM_LINK_ID_EXT])) ? $ext = $data[self::FIELD_CARE_TEAM_LINK_ID_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->addCareTeamLinkId($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRPositiveInt) {
                            $this->addCareTeamLinkId($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addCareTeamLinkId(new FHIRPositiveInt(array_merge($v, $iext)));
                            } else {
                                $this->addCareTeamLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addCareTeamLinkId(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->addCareTeamLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addCareTeamLinkId(new FHIRPositiveInt($iext));
                }
            }
        }
        if (isset($data[self::FIELD_DIAGNOSIS_LINK_ID]) || isset($data[self::FIELD_DIAGNOSIS_LINK_ID_EXT])) {
            $value = isset($data[self::FIELD_DIAGNOSIS_LINK_ID]) ? $data[self::FIELD_DIAGNOSIS_LINK_ID] : null;
            $ext = (isset($data[self::FIELD_DIAGNOSIS_LINK_ID_EXT]) && is_array($data[self::FIELD_DIAGNOSIS_LINK_ID_EXT])) ? $ext = $data[self::FIELD_DIAGNOSIS_LINK_ID_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->addDiagnosisLinkId($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRPositiveInt) {
                            $this->addDiagnosisLinkId($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addDiagnosisLinkId(new FHIRPositiveInt(array_merge($v, $iext)));
                            } else {
                                $this->addDiagnosisLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addDiagnosisLinkId(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->addDiagnosisLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addDiagnosisLinkId(new FHIRPositiveInt($iext));
                }
            }
        }
        if (isset($data[self::FIELD_PROCEDURE_LINK_ID]) || isset($data[self::FIELD_PROCEDURE_LINK_ID_EXT])) {
            $value = isset($data[self::FIELD_PROCEDURE_LINK_ID]) ? $data[self::FIELD_PROCEDURE_LINK_ID] : null;
            $ext = (isset($data[self::FIELD_PROCEDURE_LINK_ID_EXT]) && is_array($data[self::FIELD_PROCEDURE_LINK_ID_EXT])) ? $ext = $data[self::FIELD_PROCEDURE_LINK_ID_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->addProcedureLinkId($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRPositiveInt) {
                            $this->addProcedureLinkId($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addProcedureLinkId(new FHIRPositiveInt(array_merge($v, $iext)));
                            } else {
                                $this->addProcedureLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addProcedureLinkId(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->addProcedureLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addProcedureLinkId(new FHIRPositiveInt($iext));
                }
            }
        }
        if (isset($data[self::FIELD_INFORMATION_LINK_ID]) || isset($data[self::FIELD_INFORMATION_LINK_ID_EXT])) {
            $value = isset($data[self::FIELD_INFORMATION_LINK_ID]) ? $data[self::FIELD_INFORMATION_LINK_ID] : null;
            $ext = (isset($data[self::FIELD_INFORMATION_LINK_ID_EXT]) && is_array($data[self::FIELD_INFORMATION_LINK_ID_EXT])) ? $ext = $data[self::FIELD_INFORMATION_LINK_ID_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->addInformationLinkId($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRPositiveInt) {
                            $this->addInformationLinkId($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addInformationLinkId(new FHIRPositiveInt(array_merge($v, $iext)));
                            } else {
                                $this->addInformationLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addInformationLinkId(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->addInformationLinkId(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addInformationLinkId(new FHIRPositiveInt($iext));
                }
            }
        }
        if (isset($data[self::FIELD_REVENUE])) {
            if ($data[self::FIELD_REVENUE] instanceof FHIRCodeableConcept) {
                $this->setRevenue($data[self::FIELD_REVENUE]);
            } else {
                $this->setRevenue(new FHIRCodeableConcept($data[self::FIELD_REVENUE]));
            }
        }
        if (isset($data[self::FIELD_CATEGORY])) {
            if ($data[self::FIELD_CATEGORY] instanceof FHIRCodeableConcept) {
                $this->setCategory($data[self::FIELD_CATEGORY]);
            } else {
                $this->setCategory(new FHIRCodeableConcept($data[self::FIELD_CATEGORY]));
            }
        }
        if (isset($data[self::FIELD_SERVICE])) {
            if ($data[self::FIELD_SERVICE] instanceof FHIRCodeableConcept) {
                $this->setService($data[self::FIELD_SERVICE]);
            } else {
                $this->setService(new FHIRCodeableConcept($data[self::FIELD_SERVICE]));
            }
        }
        if (isset($data[self::FIELD_MODIFIER])) {
            if (is_array($data[self::FIELD_MODIFIER])) {
                foreach($data[self::FIELD_MODIFIER] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRCodeableConcept) {
                        $this->addModifier($v);
                    } else {
                        $this->addModifier(new FHIRCodeableConcept($v));
                    }
                }
            } elseif ($data[self::FIELD_MODIFIER] instanceof FHIRCodeableConcept) {
                $this->addModifier($data[self::FIELD_MODIFIER]);
            } else {
                $this->addModifier(new FHIRCodeableConcept($data[self::FIELD_MODIFIER]));
            }
        }
        if (isset($data[self::FIELD_PROGRAM_CODE])) {
            if (is_array($data[self::FIELD_PROGRAM_CODE])) {
                foreach($data[self::FIELD_PROGRAM_CODE] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRCodeableConcept) {
                        $this->addProgramCode($v);
                    } else {
                        $this->addProgramCode(new FHIRCodeableConcept($v));
                    }
                }
            } elseif ($data[self::FIELD_PROGRAM_CODE] instanceof FHIRCodeableConcept) {
                $this->addProgramCode($data[self::FIELD_PROGRAM_CODE]);
            } else {
                $this->addProgramCode(new FHIRCodeableConcept($data[self::FIELD_PROGRAM_CODE]));
            }
        }
        if (isset($data[self::FIELD_SERVICED_DATE]) || isset($data[self::FIELD_SERVICED_DATE_EXT])) {
            $value = isset($data[self::FIELD_SERVICED_DATE]) ? $data[self::FIELD_SERVICED_DATE] : null;
            $ext = (isset($data[self::FIELD_SERVICED_DATE_EXT]) && is_array($data[self::FIELD_SERVICED_DATE_EXT])) ? $ext = $data[self::FIELD_SERVICED_DATE_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRDate) {
                    $this->setServicedDate($value);
                } else if (is_array($value)) {
                    $this->setServicedDate(new FHIRDate(array_merge($ext, $value)));
                } else {
                    $this->setServicedDate(new FHIRDate([FHIRDate::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setServicedDate(new FHIRDate($ext));
            }
        }
        if (isset($data[self::FIELD_SERVICED_PERIOD])) {
            if ($data[self::FIELD_SERVICED_PERIOD] instanceof FHIRPeriod) {
                $this->setServicedPeriod($data[self::FIELD_SERVICED_PERIOD]);
            } else {
                $this->setServicedPeriod(new FHIRPeriod($data[self::FIELD_SERVICED_PERIOD]));
            }
        }
        if (isset($data[self::FIELD_LOCATION_CODEABLE_CONCEPT])) {
            if ($data[self::FIELD_LOCATION_CODEABLE_CONCEPT] instanceof FHIRCodeableConcept) {
                $this->setLocationCodeableConcept($data[self::FIELD_LOCATION_CODEABLE_CONCEPT]);
            } else {
                $this->setLocationCodeableConcept(new FHIRCodeableConcept($data[self::FIELD_LOCATION_CODEABLE_CONCEPT]));
            }
        }
        if (isset($data[self::FIELD_LOCATION_ADDRESS])) {
            if ($data[self::FIELD_LOCATION_ADDRESS] instanceof FHIRAddress) {
                $this->setLocationAddress($data[self::FIELD_LOCATION_ADDRESS]);
            } else {
                $this->setLocationAddress(new FHIRAddress($data[self::FIELD_LOCATION_ADDRESS]));
            }
        }
        if (isset($data[self::FIELD_LOCATION_REFERENCE])) {
            if ($data[self::FIELD_LOCATION_REFERENCE] instanceof FHIRReference) {
                $this->setLocationReference($data[self::FIELD_LOCATION_REFERENCE]);
            } else {
                $this->setLocationReference(new FHIRReference($data[self::FIELD_LOCATION_REFERENCE]));
            }
        }
        if (isset($data[self::FIELD_QUANTITY])) {
            if ($data[self::FIELD_QUANTITY] instanceof FHIRQuantity) {
                $this->setQuantity($data[self::FIELD_QUANTITY]);
            } else {
                $this->setQuantity(new FHIRQuantity($data[self::FIELD_QUANTITY]));
            }
        }
        if (isset($data[self::FIELD_UNIT_PRICE])) {
            if ($data[self::FIELD_UNIT_PRICE] instanceof FHIRMoney) {
                $this->setUnitPrice($data[self::FIELD_UNIT_PRICE]);
            } else {
                $this->setUnitPrice(new FHIRMoney($data[self::FIELD_UNIT_PRICE]));
            }
        }
        if (isset($data[self::FIELD_FACTOR]) || isset($data[self::FIELD_FACTOR_EXT])) {
            $value = isset($data[self::FIELD_FACTOR]) ? $data[self::FIELD_FACTOR] : null;
            $ext = (isset($data[self::FIELD_FACTOR_EXT]) && is_array($data[self::FIELD_FACTOR_EXT])) ? $ext = $data[self::FIELD_FACTOR_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRDecimal) {
                    $this->setFactor($value);
                } else if (is_array($value)) {
                    $this->setFactor(new FHIRDecimal(array_merge($ext, $value)));
                } else {
                    $this->setFactor(new FHIRDecimal([FHIRDecimal::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setFactor(new FHIRDecimal($ext));
            }
        }
        if (isset($data[self::FIELD_NET])) {
            if ($data[self::FIELD_NET] instanceof FHIRMoney) {
                $this->setNet($data[self::FIELD_NET]);
            } else {
                $this->setNet(new FHIRMoney($data[self::FIELD_NET]));
            }
        }
        if (isset($data[self::FIELD_UDI])) {
            if (is_array($data[self::FIELD_UDI])) {
                foreach($data[self::FIELD_UDI] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRReference) {
                        $this->addUdi($v);
                    } else {
                        $this->addUdi(new FHIRReference($v));
                    }
                }
            } elseif ($data[self::FIELD_UDI] instanceof FHIRReference) {
                $this->addUdi($data[self::FIELD_UDI]);
            } else {
                $this->addUdi(new FHIRReference($data[self::FIELD_UDI]));
            }
        }
        if (isset($data[self::FIELD_BODY_SITE])) {
            if ($data[self::FIELD_BODY_SITE] instanceof FHIRCodeableConcept) {
                $this->setBodySite($data[self::FIELD_BODY_SITE]);
            } else {
                $this->setBodySite(new FHIRCodeableConcept($data[self::FIELD_BODY_SITE]));
            }
        }
        if (isset($data[self::FIELD_SUB_SITE])) {
            if (is_array($data[self::FIELD_SUB_SITE])) {
                foreach($data[self::FIELD_SUB_SITE] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRCodeableConcept) {
                        $this->addSubSite($v);
                    } else {
                        $this->addSubSite(new FHIRCodeableConcept($v));
                    }
                }
            } elseif ($data[self::FIELD_SUB_SITE] instanceof FHIRCodeableConcept) {
                $this->addSubSite($data[self::FIELD_SUB_SITE]);
            } else {
                $this->addSubSite(new FHIRCodeableConcept($data[self::FIELD_SUB_SITE]));
            }
        }
        if (isset($data[self::FIELD_ENCOUNTER])) {
            if (is_array($data[self::FIELD_ENCOUNTER])) {
                foreach($data[self::FIELD_ENCOUNTER] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRReference) {
                        $this->addEncounter($v);
                    } else {
                        $this->addEncounter(new FHIRReference($v));
                    }
                }
            } elseif ($data[self::FIELD_ENCOUNTER] instanceof FHIRReference) {
                $this->addEncounter($data[self::FIELD_ENCOUNTER]);
            } else {
                $this->addEncounter(new FHIRReference($data[self::FIELD_ENCOUNTER]));
            }
        }
        if (isset($data[self::FIELD_NOTE_NUMBER]) || isset($data[self::FIELD_NOTE_NUMBER_EXT])) {
            $value = isset($data[self::FIELD_NOTE_NUMBER]) ? $data[self::FIELD_NOTE_NUMBER] : null;
            $ext = (isset($data[self::FIELD_NOTE_NUMBER_EXT]) && is_array($data[self::FIELD_NOTE_NUMBER_EXT])) ? $ext = $data[self::FIELD_NOTE_NUMBER_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRPositiveInt) {
                    $this->addNoteNumber($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRPositiveInt) {
                            $this->addNoteNumber($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addNoteNumber(new FHIRPositiveInt(array_merge($v, $iext)));
                            } else {
                                $this->addNoteNumber(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addNoteNumber(new FHIRPositiveInt(array_merge($ext, $value)));
                } else {
                    $this->addNoteNumber(new FHIRPositiveInt([FHIRPositiveInt::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addNoteNumber(new FHIRPositiveInt($iext));
                }
            }
        }
        if (isset($data[self::FIELD_ADJUDICATION])) {
            if (is_array($data[self::FIELD_ADJUDICATION])) {
                foreach($data[self::FIELD_ADJUDICATION] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRExplanationOfBenefitAdjudication) {
                        $this->addAdjudication($v);
                    } else {
                        $this->addAdjudication(new FHIRExplanationOfBenefitAdjudication($v));
                    }
                }
            } elseif ($data[self::FIELD_ADJUDICATION] instanceof FHIRExplanationOfBenefitAdjudication) {
                $this->addAdjudication($data[self::FIELD_ADJUDICATION]);
            } else {
                $this->addAdjudication(new FHIRExplanationOfBenefitAdjudication($data[self::FIELD_ADJUDICATION]));
            }
        }
        if (isset($data[self::FIELD_DETAIL])) {
            if (is_array($data[self::FIELD_DETAIL])) {
                foreach($data[self::FIELD_DETAIL] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRExplanationOfBenefitDetail) {
                        $this->addDetail($v);
                    } else {
                        $this->addDetail(new FHIRExplanationOfBenefitDetail($v));
                    }
                }
            } elseif ($data[self::FIELD_DETAIL] instanceof FHIRExplanationOfBenefitDetail) {
                $this->addDetail($data[self::FIELD_DETAIL]);
            } else {
                $this->addDetail(new FHIRExplanationOfBenefitDetail($data[self::FIELD_DETAIL]));
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
        return "<ExplanationOfBenefitItem{$xmlns}></ExplanationOfBenefitItem>";
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A service line number.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A service line number.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $sequence
     * @return static
     */
    public function setSequence($sequence = null)
    {
        if (null !== $sequence && !($sequence instanceof FHIRPositiveInt)) {
            $sequence = new FHIRPositiveInt($sequence);
        }
        $this->_trackValueSet($this->sequence, $sequence);
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Careteam applicable for this service or product line.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    public function getCareTeamLinkId()
    {
        return $this->careTeamLinkId;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Careteam applicable for this service or product line.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $careTeamLinkId
     * @return static
     */
    public function addCareTeamLinkId($careTeamLinkId = null)
    {
        if (null !== $careTeamLinkId && !($careTeamLinkId instanceof FHIRPositiveInt)) {
            $careTeamLinkId = new FHIRPositiveInt($careTeamLinkId);
        }
        $this->_trackValueAdded();
        $this->careTeamLinkId[] = $careTeamLinkId;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Careteam applicable for this service or product line.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[] $careTeamLinkId
     * @return static
     */
    public function setCareTeamLinkId(array $careTeamLinkId = [])
    {
        if ([] !== $this->careTeamLinkId) {
            $this->_trackValuesRemoved(count($this->careTeamLinkId));
            $this->careTeamLinkId = [];
        }
        if ([] === $careTeamLinkId) {
            return $this;
        }
        foreach($careTeamLinkId as $v) {
            if ($v instanceof FHIRPositiveInt) {
                $this->addCareTeamLinkId($v);
            } else {
                $this->addCareTeamLinkId(new FHIRPositiveInt($v));
            }
        }
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Diagnosis applicable for this service or product line.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    public function getDiagnosisLinkId()
    {
        return $this->diagnosisLinkId;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Diagnosis applicable for this service or product line.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $diagnosisLinkId
     * @return static
     */
    public function addDiagnosisLinkId($diagnosisLinkId = null)
    {
        if (null !== $diagnosisLinkId && !($diagnosisLinkId instanceof FHIRPositiveInt)) {
            $diagnosisLinkId = new FHIRPositiveInt($diagnosisLinkId);
        }
        $this->_trackValueAdded();
        $this->diagnosisLinkId[] = $diagnosisLinkId;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Diagnosis applicable for this service or product line.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[] $diagnosisLinkId
     * @return static
     */
    public function setDiagnosisLinkId(array $diagnosisLinkId = [])
    {
        if ([] !== $this->diagnosisLinkId) {
            $this->_trackValuesRemoved(count($this->diagnosisLinkId));
            $this->diagnosisLinkId = [];
        }
        if ([] === $diagnosisLinkId) {
            return $this;
        }
        foreach($diagnosisLinkId as $v) {
            if ($v instanceof FHIRPositiveInt) {
                $this->addDiagnosisLinkId($v);
            } else {
                $this->addDiagnosisLinkId(new FHIRPositiveInt($v));
            }
        }
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Procedures applicable for this service or product line.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    public function getProcedureLinkId()
    {
        return $this->procedureLinkId;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Procedures applicable for this service or product line.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $procedureLinkId
     * @return static
     */
    public function addProcedureLinkId($procedureLinkId = null)
    {
        if (null !== $procedureLinkId && !($procedureLinkId instanceof FHIRPositiveInt)) {
            $procedureLinkId = new FHIRPositiveInt($procedureLinkId);
        }
        $this->_trackValueAdded();
        $this->procedureLinkId[] = $procedureLinkId;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Procedures applicable for this service or product line.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[] $procedureLinkId
     * @return static
     */
    public function setProcedureLinkId(array $procedureLinkId = [])
    {
        if ([] !== $this->procedureLinkId) {
            $this->_trackValuesRemoved(count($this->procedureLinkId));
            $this->procedureLinkId = [];
        }
        if ([] === $procedureLinkId) {
            return $this;
        }
        foreach($procedureLinkId as $v) {
            if ($v instanceof FHIRPositiveInt) {
                $this->addProcedureLinkId($v);
            } else {
                $this->addProcedureLinkId(new FHIRPositiveInt($v));
            }
        }
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Exceptions, special conditions and supporting information pplicable for this
     * service or product line.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    public function getInformationLinkId()
    {
        return $this->informationLinkId;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Exceptions, special conditions and supporting information pplicable for this
     * service or product line.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $informationLinkId
     * @return static
     */
    public function addInformationLinkId($informationLinkId = null)
    {
        if (null !== $informationLinkId && !($informationLinkId instanceof FHIRPositiveInt)) {
            $informationLinkId = new FHIRPositiveInt($informationLinkId);
        }
        $this->_trackValueAdded();
        $this->informationLinkId[] = $informationLinkId;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * Exceptions, special conditions and supporting information pplicable for this
     * service or product line.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[] $informationLinkId
     * @return static
     */
    public function setInformationLinkId(array $informationLinkId = [])
    {
        if ([] !== $this->informationLinkId) {
            $this->_trackValuesRemoved(count($this->informationLinkId));
            $this->informationLinkId = [];
        }
        if ([] === $informationLinkId) {
            return $this;
        }
        foreach($informationLinkId as $v) {
            if ($v instanceof FHIRPositiveInt) {
                $this->addInformationLinkId($v);
            } else {
                $this->addInformationLinkId(new FHIRPositiveInt($v));
            }
        }
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The type of reveneu or cost center providing the product and/or service.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The type of reveneu or cost center providing the product and/or service.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $revenue
     * @return static
     */
    public function setRevenue(FHIRCodeableConcept $revenue = null)
    {
        $this->_trackValueSet($this->revenue, $revenue);
        $this->revenue = $revenue;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Health Care Service Type Codes to identify the classification of service or
     * benefits.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Health Care Service Type Codes to identify the classification of service or
     * benefits.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $category
     * @return static
     */
    public function setCategory(FHIRCodeableConcept $category = null)
    {
        $this->_trackValueSet($this->category, $category);
        $this->category = $category;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If this is an actual service or product line, ie. not a Group, then use code to
     * indicate the Professional Service or Product supplied (eg. CTP,
     * HCPCS,USCLS,ICD10, NCPDP,DIN,ACHI,CCI). If a grouping item then use a group code
     * to indicate the type of thing being grouped eg. 'glasses' or 'compound'.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If this is an actual service or product line, ie. not a Group, then use code to
     * indicate the Professional Service or Product supplied (eg. CTP,
     * HCPCS,USCLS,ICD10, NCPDP,DIN,ACHI,CCI). If a grouping item then use a group code
     * to indicate the type of thing being grouped eg. 'glasses' or 'compound'.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $service
     * @return static
     */
    public function setService(FHIRCodeableConcept $service = null)
    {
        $this->_trackValueSet($this->service, $service);
        $this->service = $service;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Item typification or modifiers codes, eg for Oral whether the treatment is
     * cosmetic or associated with TMJ, or for medical whether the treatment was
     * outside the clinic or out of office hours.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Item typification or modifiers codes, eg for Oral whether the treatment is
     * cosmetic or associated with TMJ, or for medical whether the treatment was
     * outside the clinic or out of office hours.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $modifier
     * @return static
     */
    public function addModifier(FHIRCodeableConcept $modifier = null)
    {
        $this->_trackValueAdded();
        $this->modifier[] = $modifier;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Item typification or modifiers codes, eg for Oral whether the treatment is
     * cosmetic or associated with TMJ, or for medical whether the treatment was
     * outside the clinic or out of office hours.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[] $modifier
     * @return static
     */
    public function setModifier(array $modifier = [])
    {
        if ([] !== $this->modifier) {
            $this->_trackValuesRemoved(count($this->modifier));
            $this->modifier = [];
        }
        if ([] === $modifier) {
            return $this;
        }
        foreach($modifier as $v) {
            if ($v instanceof FHIRCodeableConcept) {
                $this->addModifier($v);
            } else {
                $this->addModifier(new FHIRCodeableConcept($v));
            }
        }
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * For programs which require reson codes for the inclusion, covering, of this
     * billed item under the program or sub-program.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    public function getProgramCode()
    {
        return $this->programCode;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * For programs which require reson codes for the inclusion, covering, of this
     * billed item under the program or sub-program.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $programCode
     * @return static
     */
    public function addProgramCode(FHIRCodeableConcept $programCode = null)
    {
        $this->_trackValueAdded();
        $this->programCode[] = $programCode;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * For programs which require reson codes for the inclusion, covering, of this
     * billed item under the program or sub-program.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[] $programCode
     * @return static
     */
    public function setProgramCode(array $programCode = [])
    {
        if ([] !== $this->programCode) {
            $this->_trackValuesRemoved(count($this->programCode));
            $this->programCode = [];
        }
        if ([] === $programCode) {
            return $this;
        }
        foreach($programCode as $v) {
            if ($v instanceof FHIRCodeableConcept) {
                $this->addProgramCode($v);
            } else {
                $this->addProgramCode(new FHIRCodeableConcept($v));
            }
        }
        return $this;
    }

    /**
     * A date or partial date (e.g. just year or year + month). There is no time zone.
     * The format is a union of the schema types gYear, gYearMonth and date. Dates
     * SHALL be valid dates.
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRDate
     */
    public function getServicedDate()
    {
        return $this->servicedDate;
    }

    /**
     * A date or partial date (e.g. just year or year + month). There is no time zone.
     * The format is a union of the schema types gYear, gYearMonth and date. Dates
     * SHALL be valid dates.
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRDate $servicedDate
     * @return static
     */
    public function setServicedDate($servicedDate = null)
    {
        if (null !== $servicedDate && !($servicedDate instanceof FHIRDate)) {
            $servicedDate = new FHIRDate($servicedDate);
        }
        $this->_trackValueSet($this->servicedDate, $servicedDate);
        $this->servicedDate = $servicedDate;
        return $this;
    }

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPeriod
     */
    public function getServicedPeriod()
    {
        return $this->servicedPeriod;
    }

    /**
     * A time period defined by a start and end date and optionally time.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The date or dates when the enclosed suite of services were performed or
     * completed.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPeriod $servicedPeriod
     * @return static
     */
    public function setServicedPeriod(FHIRPeriod $servicedPeriod = null)
    {
        $this->_trackValueSet($this->servicedPeriod, $servicedPeriod);
        $this->servicedPeriod = $servicedPeriod;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    public function getLocationCodeableConcept()
    {
        return $this->locationCodeableConcept;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $locationCodeableConcept
     * @return static
     */
    public function setLocationCodeableConcept(FHIRCodeableConcept $locationCodeableConcept = null)
    {
        $this->_trackValueSet($this->locationCodeableConcept, $locationCodeableConcept);
        $this->locationCodeableConcept = $locationCodeableConcept;
        return $this;
    }

    /**
     * An address expressed using postal conventions (as opposed to GPS or other
     * location definition formats). This data type may be used to convey addresses for
     * use in delivering mail as well as for visiting locations which might not be
     * valid for mail delivery. There are a variety of postal address formats defined
     * around the world.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRAddress
     */
    public function getLocationAddress()
    {
        return $this->locationAddress;
    }

    /**
     * An address expressed using postal conventions (as opposed to GPS or other
     * location definition formats). This data type may be used to convey addresses for
     * use in delivering mail as well as for visiting locations which might not be
     * valid for mail delivery. There are a variety of postal address formats defined
     * around the world.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRAddress $locationAddress
     * @return static
     */
    public function setLocationAddress(FHIRAddress $locationAddress = null)
    {
        $this->_trackValueSet($this->locationAddress, $locationAddress);
        $this->locationAddress = $locationAddress;
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    public function getLocationReference()
    {
        return $this->locationReference;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the service was provided.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRReference $locationReference
     * @return static
     */
    public function setLocationReference(FHIRReference $locationReference = null)
    {
        $this->_trackValueSet($this->locationReference, $locationReference);
        $this->locationReference = $locationReference;
        return $this;
    }

    /**
     * A measured amount (or an amount that can potentially be measured). Note that
     * measured amounts include amounts that are not precisely quantified, including
     * amounts involving arbitrary units and floating currencies.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The number of repetitions of a service or product.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity
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
     * The number of repetitions of a service or product.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity $quantity
     * @return static
     */
    public function setQuantity(FHIRQuantity $quantity = null)
    {
        $this->_trackValueSet($this->quantity, $quantity);
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If the item is a node then this is the fee for the product or service, otherwise
     * this is the total of the fees for the children of the group.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * If the item is a node then this is the fee for the product or service, otherwise
     * this is the total of the fees for the children of the group.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney $unitPrice
     * @return static
     */
    public function setUnitPrice(FHIRMoney $unitPrice = null)
    {
        $this->_trackValueSet($this->unitPrice, $unitPrice);
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * A rational number with implicit precision
     * Do not use a IEEE type floating point type, instead use something that works
     * like a true decimal, with inbuilt precision (e.g. Java BigInteger)
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A real number that represents a multiplier used in determining the overall value
     * of services delivered and/or goods received. The concept of a Factor allows for
     * a discount or surcharge multiplier to be applied to a monetary amount.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRDecimal
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * A rational number with implicit precision
     * Do not use a IEEE type floating point type, instead use something that works
     * like a true decimal, with inbuilt precision (e.g. Java BigInteger)
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * A real number that represents a multiplier used in determining the overall value
     * of services delivered and/or goods received. The concept of a Factor allows for
     * a discount or surcharge multiplier to be applied to a monetary amount.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRDecimal $factor
     * @return static
     */
    public function setFactor($factor = null)
    {
        if (null !== $factor && !($factor instanceof FHIRDecimal)) {
            $factor = new FHIRDecimal($factor);
        }
        $this->_trackValueSet($this->factor, $factor);
        $this->factor = $factor;
        return $this;
    }

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The quantity times the unit price for an addittional service or product or
     * charge. For example, the formula: unit Quantity * unit Price (Cost per Point) *
     * factor Number * points = net Amount. Quantity, factor and points are assumed to
     * be 1 if not supplied.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * An amount of economic utility in some recognized currency.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The quantity times the unit price for an addittional service or product or
     * charge. For example, the formula: unit Quantity * unit Price (Cost per Point) *
     * factor Number * points = net Amount. Quantity, factor and points are assumed to
     * be 1 if not supplied.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRQuantity\FHIRMoney $net
     * @return static
     */
    public function setNet(FHIRMoney $net = null)
    {
        $this->_trackValueSet($this->net, $net);
        $this->net = $net;
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * List of Unique Device Identifiers associated with this line item.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRReference[]
     */
    public function getUdi()
    {
        return $this->udi;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * List of Unique Device Identifiers associated with this line item.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRReference $udi
     * @return static
     */
    public function addUdi(FHIRReference $udi = null)
    {
        $this->_trackValueAdded();
        $this->udi[] = $udi;
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * List of Unique Device Identifiers associated with this line item.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRReference[] $udi
     * @return static
     */
    public function setUdi(array $udi = [])
    {
        if ([] !== $this->udi) {
            $this->_trackValuesRemoved(count($this->udi));
            $this->udi = [];
        }
        if ([] === $udi) {
            return $this;
        }
        foreach($udi as $v) {
            if ($v instanceof FHIRReference) {
                $this->addUdi($v);
            } else {
                $this->addUdi(new FHIRReference($v));
            }
        }
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Physical service site on the patient (limb, tooth, etc).
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept
     */
    public function getBodySite()
    {
        return $this->bodySite;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Physical service site on the patient (limb, tooth, etc).
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $bodySite
     * @return static
     */
    public function setBodySite(FHIRCodeableConcept $bodySite = null)
    {
        $this->_trackValueSet($this->bodySite, $bodySite);
        $this->bodySite = $bodySite;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A region or surface of the site, eg. limb region or tooth surface(s).
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    public function getSubSite()
    {
        return $this->subSite;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A region or surface of the site, eg. limb region or tooth surface(s).
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $subSite
     * @return static
     */
    public function addSubSite(FHIRCodeableConcept $subSite = null)
    {
        $this->_trackValueAdded();
        $this->subSite[] = $subSite;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A region or surface of the site, eg. limb region or tooth surface(s).
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[] $subSite
     * @return static
     */
    public function setSubSite(array $subSite = [])
    {
        if ([] !== $this->subSite) {
            $this->_trackValuesRemoved(count($this->subSite));
            $this->subSite = [];
        }
        if ([] === $subSite) {
            return $this;
        }
        foreach($subSite as $v) {
            if ($v instanceof FHIRCodeableConcept) {
                $this->addSubSite($v);
            } else {
                $this->addSubSite(new FHIRCodeableConcept($v));
            }
        }
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A billed item may include goods or services provided in multiple encounters.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRReference[]
     */
    public function getEncounter()
    {
        return $this->encounter;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A billed item may include goods or services provided in multiple encounters.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRReference $encounter
     * @return static
     */
    public function addEncounter(FHIRReference $encounter = null)
    {
        $this->_trackValueAdded();
        $this->encounter[] = $encounter;
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * A billed item may include goods or services provided in multiple encounters.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRReference[] $encounter
     * @return static
     */
    public function setEncounter(array $encounter = [])
    {
        if ([] !== $this->encounter) {
            $this->_trackValuesRemoved(count($this->encounter));
            $this->encounter = [];
        }
        if ([] === $encounter) {
            return $this;
        }
        foreach($encounter as $v) {
            if ($v instanceof FHIRReference) {
                $this->addEncounter($v);
            } else {
                $this->addEncounter(new FHIRReference($v));
            }
        }
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A list of note references to the notes provided below.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[]
     */
    public function getNoteNumber()
    {
        return $this->noteNumber;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A list of note references to the notes provided below.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt $noteNumber
     * @return static
     */
    public function addNoteNumber($noteNumber = null)
    {
        if (null !== $noteNumber && !($noteNumber instanceof FHIRPositiveInt)) {
            $noteNumber = new FHIRPositiveInt($noteNumber);
        }
        $this->_trackValueAdded();
        $this->noteNumber[] = $noteNumber;
        return $this;
    }

    /**
     * An integer with a value that is positive (e.g. >0)
     * If the element is present, it must have either a \@value, an \@id referenced from
     * the Narrative, or extensions
     *
     * A list of note references to the notes provided below.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRPositiveInt[] $noteNumber
     * @return static
     */
    public function setNoteNumber(array $noteNumber = [])
    {
        if ([] !== $this->noteNumber) {
            $this->_trackValuesRemoved(count($this->noteNumber));
            $this->noteNumber = [];
        }
        if ([] === $noteNumber) {
            return $this;
        }
        foreach($noteNumber as $v) {
            if ($v instanceof FHIRPositiveInt) {
                $this->addNoteNumber($v);
            } else {
                $this->addNoteNumber(new FHIRPositiveInt($v));
            }
        }
        return $this;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * The adjudications results.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitAdjudication[]
     */
    public function getAdjudication()
    {
        return $this->adjudication;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * The adjudications results.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitAdjudication $adjudication
     * @return static
     */
    public function addAdjudication(FHIRExplanationOfBenefitAdjudication $adjudication = null)
    {
        $this->_trackValueAdded();
        $this->adjudication[] = $adjudication;
        return $this;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * The adjudications results.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitAdjudication[] $adjudication
     * @return static
     */
    public function setAdjudication(array $adjudication = [])
    {
        if ([] !== $this->adjudication) {
            $this->_trackValuesRemoved(count($this->adjudication));
            $this->adjudication = [];
        }
        if ([] === $adjudication) {
            return $this;
        }
        foreach($adjudication as $v) {
            if ($v instanceof FHIRExplanationOfBenefitAdjudication) {
                $this->addAdjudication($v);
            } else {
                $this->addAdjudication(new FHIRExplanationOfBenefitAdjudication($v));
            }
        }
        return $this;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * Second tier of goods and services.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitDetail[]
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * Second tier of goods and services.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitDetail $detail
     * @return static
     */
    public function addDetail(FHIRExplanationOfBenefitDetail $detail = null)
    {
        $this->_trackValueAdded();
        $this->detail[] = $detail;
        return $this;
    }

    /**
     * This resource provides: the claim details; adjudication details from the
     * processing of a Claim; and optionally account balance information, for informing
     * the subscriber of the benefits provided.
     *
     * Second tier of goods and services.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitDetail[] $detail
     * @return static
     */
    public function setDetail(array $detail = [])
    {
        if ([] !== $this->detail) {
            $this->_trackValuesRemoved(count($this->detail));
            $this->detail = [];
        }
        if ([] === $detail) {
            return $this;
        }
        foreach($detail as $v) {
            if ($v instanceof FHIRExplanationOfBenefitDetail) {
                $this->addDetail($v);
            } else {
                $this->addDetail(new FHIRExplanationOfBenefitDetail($v));
            }
        }
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
        if (null !== ($v = $this->getSequence())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_SEQUENCE] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getCareTeamLinkId())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_CARE_TEAM_LINK_ID, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getDiagnosisLinkId())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_DIAGNOSIS_LINK_ID, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getProcedureLinkId())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_PROCEDURE_LINK_ID, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getInformationLinkId())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_INFORMATION_LINK_ID, $i)] = $fieldErrs;
                }
            }
        }
        if (null !== ($v = $this->getRevenue())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_REVENUE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getCategory())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_CATEGORY] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getService())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_SERVICE] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getModifier())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_MODIFIER, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getProgramCode())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_PROGRAM_CODE, $i)] = $fieldErrs;
                }
            }
        }
        if (null !== ($v = $this->getServicedDate())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_SERVICED_DATE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getServicedPeriod())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_SERVICED_PERIOD] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getLocationCodeableConcept())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_LOCATION_CODEABLE_CONCEPT] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getLocationAddress())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_LOCATION_ADDRESS] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getLocationReference())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_LOCATION_REFERENCE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getQuantity())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_QUANTITY] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getUnitPrice())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_UNIT_PRICE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getFactor())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_FACTOR] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getNet())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_NET] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getUdi())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_UDI, $i)] = $fieldErrs;
                }
            }
        }
        if (null !== ($v = $this->getBodySite())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_BODY_SITE] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getSubSite())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_SUB_SITE, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getEncounter())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_ENCOUNTER, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getNoteNumber())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_NOTE_NUMBER, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getAdjudication())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_ADJUDICATION, $i)] = $fieldErrs;
                }
            }
        }
        if ([] !== ($vs = $this->getDetail())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_DETAIL, $i)] = $fieldErrs;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SEQUENCE])) {
            $v = $this->getSequence();
            foreach($validationRules[self::FIELD_SEQUENCE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_SEQUENCE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SEQUENCE])) {
                        $errs[self::FIELD_SEQUENCE] = [];
                    }
                    $errs[self::FIELD_SEQUENCE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_CARE_TEAM_LINK_ID])) {
            $v = $this->getCareTeamLinkId();
            foreach($validationRules[self::FIELD_CARE_TEAM_LINK_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_CARE_TEAM_LINK_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_CARE_TEAM_LINK_ID])) {
                        $errs[self::FIELD_CARE_TEAM_LINK_ID] = [];
                    }
                    $errs[self::FIELD_CARE_TEAM_LINK_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_DIAGNOSIS_LINK_ID])) {
            $v = $this->getDiagnosisLinkId();
            foreach($validationRules[self::FIELD_DIAGNOSIS_LINK_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_DIAGNOSIS_LINK_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_DIAGNOSIS_LINK_ID])) {
                        $errs[self::FIELD_DIAGNOSIS_LINK_ID] = [];
                    }
                    $errs[self::FIELD_DIAGNOSIS_LINK_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_PROCEDURE_LINK_ID])) {
            $v = $this->getProcedureLinkId();
            foreach($validationRules[self::FIELD_PROCEDURE_LINK_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_PROCEDURE_LINK_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_PROCEDURE_LINK_ID])) {
                        $errs[self::FIELD_PROCEDURE_LINK_ID] = [];
                    }
                    $errs[self::FIELD_PROCEDURE_LINK_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_INFORMATION_LINK_ID])) {
            $v = $this->getInformationLinkId();
            foreach($validationRules[self::FIELD_INFORMATION_LINK_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_INFORMATION_LINK_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_INFORMATION_LINK_ID])) {
                        $errs[self::FIELD_INFORMATION_LINK_ID] = [];
                    }
                    $errs[self::FIELD_INFORMATION_LINK_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_REVENUE])) {
            $v = $this->getRevenue();
            foreach($validationRules[self::FIELD_REVENUE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_REVENUE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_REVENUE])) {
                        $errs[self::FIELD_REVENUE] = [];
                    }
                    $errs[self::FIELD_REVENUE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_CATEGORY])) {
            $v = $this->getCategory();
            foreach($validationRules[self::FIELD_CATEGORY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_CATEGORY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_CATEGORY])) {
                        $errs[self::FIELD_CATEGORY] = [];
                    }
                    $errs[self::FIELD_CATEGORY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SERVICE])) {
            $v = $this->getService();
            foreach($validationRules[self::FIELD_SERVICE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_SERVICE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SERVICE])) {
                        $errs[self::FIELD_SERVICE] = [];
                    }
                    $errs[self::FIELD_SERVICE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_MODIFIER])) {
            $v = $this->getModifier();
            foreach($validationRules[self::FIELD_MODIFIER] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_MODIFIER, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_MODIFIER])) {
                        $errs[self::FIELD_MODIFIER] = [];
                    }
                    $errs[self::FIELD_MODIFIER][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_PROGRAM_CODE])) {
            $v = $this->getProgramCode();
            foreach($validationRules[self::FIELD_PROGRAM_CODE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_PROGRAM_CODE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_PROGRAM_CODE])) {
                        $errs[self::FIELD_PROGRAM_CODE] = [];
                    }
                    $errs[self::FIELD_PROGRAM_CODE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SERVICED_DATE])) {
            $v = $this->getServicedDate();
            foreach($validationRules[self::FIELD_SERVICED_DATE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_SERVICED_DATE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SERVICED_DATE])) {
                        $errs[self::FIELD_SERVICED_DATE] = [];
                    }
                    $errs[self::FIELD_SERVICED_DATE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SERVICED_PERIOD])) {
            $v = $this->getServicedPeriod();
            foreach($validationRules[self::FIELD_SERVICED_PERIOD] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_SERVICED_PERIOD, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SERVICED_PERIOD])) {
                        $errs[self::FIELD_SERVICED_PERIOD] = [];
                    }
                    $errs[self::FIELD_SERVICED_PERIOD][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_LOCATION_CODEABLE_CONCEPT])) {
            $v = $this->getLocationCodeableConcept();
            foreach($validationRules[self::FIELD_LOCATION_CODEABLE_CONCEPT] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_LOCATION_CODEABLE_CONCEPT, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_LOCATION_CODEABLE_CONCEPT])) {
                        $errs[self::FIELD_LOCATION_CODEABLE_CONCEPT] = [];
                    }
                    $errs[self::FIELD_LOCATION_CODEABLE_CONCEPT][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_LOCATION_ADDRESS])) {
            $v = $this->getLocationAddress();
            foreach($validationRules[self::FIELD_LOCATION_ADDRESS] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_LOCATION_ADDRESS, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_LOCATION_ADDRESS])) {
                        $errs[self::FIELD_LOCATION_ADDRESS] = [];
                    }
                    $errs[self::FIELD_LOCATION_ADDRESS][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_LOCATION_REFERENCE])) {
            $v = $this->getLocationReference();
            foreach($validationRules[self::FIELD_LOCATION_REFERENCE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_LOCATION_REFERENCE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_LOCATION_REFERENCE])) {
                        $errs[self::FIELD_LOCATION_REFERENCE] = [];
                    }
                    $errs[self::FIELD_LOCATION_REFERENCE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_QUANTITY])) {
            $v = $this->getQuantity();
            foreach($validationRules[self::FIELD_QUANTITY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_QUANTITY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_QUANTITY])) {
                        $errs[self::FIELD_QUANTITY] = [];
                    }
                    $errs[self::FIELD_QUANTITY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_UNIT_PRICE])) {
            $v = $this->getUnitPrice();
            foreach($validationRules[self::FIELD_UNIT_PRICE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_UNIT_PRICE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_UNIT_PRICE])) {
                        $errs[self::FIELD_UNIT_PRICE] = [];
                    }
                    $errs[self::FIELD_UNIT_PRICE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_FACTOR])) {
            $v = $this->getFactor();
            foreach($validationRules[self::FIELD_FACTOR] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_FACTOR, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_FACTOR])) {
                        $errs[self::FIELD_FACTOR] = [];
                    }
                    $errs[self::FIELD_FACTOR][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_NET])) {
            $v = $this->getNet();
            foreach($validationRules[self::FIELD_NET] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_NET, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_NET])) {
                        $errs[self::FIELD_NET] = [];
                    }
                    $errs[self::FIELD_NET][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_UDI])) {
            $v = $this->getUdi();
            foreach($validationRules[self::FIELD_UDI] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_UDI, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_UDI])) {
                        $errs[self::FIELD_UDI] = [];
                    }
                    $errs[self::FIELD_UDI][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_BODY_SITE])) {
            $v = $this->getBodySite();
            foreach($validationRules[self::FIELD_BODY_SITE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_BODY_SITE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_BODY_SITE])) {
                        $errs[self::FIELD_BODY_SITE] = [];
                    }
                    $errs[self::FIELD_BODY_SITE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_SUB_SITE])) {
            $v = $this->getSubSite();
            foreach($validationRules[self::FIELD_SUB_SITE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_SUB_SITE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_SUB_SITE])) {
                        $errs[self::FIELD_SUB_SITE] = [];
                    }
                    $errs[self::FIELD_SUB_SITE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_ENCOUNTER])) {
            $v = $this->getEncounter();
            foreach($validationRules[self::FIELD_ENCOUNTER] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_ENCOUNTER, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_ENCOUNTER])) {
                        $errs[self::FIELD_ENCOUNTER] = [];
                    }
                    $errs[self::FIELD_ENCOUNTER][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_NOTE_NUMBER])) {
            $v = $this->getNoteNumber();
            foreach($validationRules[self::FIELD_NOTE_NUMBER] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_NOTE_NUMBER, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_NOTE_NUMBER])) {
                        $errs[self::FIELD_NOTE_NUMBER] = [];
                    }
                    $errs[self::FIELD_NOTE_NUMBER][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_ADJUDICATION])) {
            $v = $this->getAdjudication();
            foreach($validationRules[self::FIELD_ADJUDICATION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_ADJUDICATION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_ADJUDICATION])) {
                        $errs[self::FIELD_ADJUDICATION] = [];
                    }
                    $errs[self::FIELD_ADJUDICATION][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_DETAIL])) {
            $v = $this->getDetail();
            foreach($validationRules[self::FIELD_DETAIL] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_EXPLANATION_OF_BENEFIT_DOT_ITEM, self::FIELD_DETAIL, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_DETAIL])) {
                        $errs[self::FIELD_DETAIL] = [];
                    }
                    $errs[self::FIELD_DETAIL][$rule] = $err;
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
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitItem $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitItem
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
                throw new \DomainException(sprintf('FHIRExplanationOfBenefitItem::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRExplanationOfBenefitItem::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRExplanationOfBenefitItem(null);
        } elseif (!is_object($type) || !($type instanceof FHIRExplanationOfBenefitItem)) {
            throw new \RuntimeException(sprintf(
                'FHIRExplanationOfBenefitItem::xmlUnserialize - $type must be instance of \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRExplanationOfBenefit\FHIRExplanationOfBenefitItem or null, %s seen.',
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
            if (self::FIELD_SEQUENCE === $n->nodeName) {
                $type->setSequence(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_CARE_TEAM_LINK_ID === $n->nodeName) {
                $type->addCareTeamLinkId(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_DIAGNOSIS_LINK_ID === $n->nodeName) {
                $type->addDiagnosisLinkId(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_PROCEDURE_LINK_ID === $n->nodeName) {
                $type->addProcedureLinkId(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_INFORMATION_LINK_ID === $n->nodeName) {
                $type->addInformationLinkId(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_REVENUE === $n->nodeName) {
                $type->setRevenue(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_CATEGORY === $n->nodeName) {
                $type->setCategory(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_SERVICE === $n->nodeName) {
                $type->setService(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER === $n->nodeName) {
                $type->addModifier(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_PROGRAM_CODE === $n->nodeName) {
                $type->addProgramCode(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_SERVICED_DATE === $n->nodeName) {
                $type->setServicedDate(FHIRDate::xmlUnserialize($n));
            } elseif (self::FIELD_SERVICED_PERIOD === $n->nodeName) {
                $type->setServicedPeriod(FHIRPeriod::xmlUnserialize($n));
            } elseif (self::FIELD_LOCATION_CODEABLE_CONCEPT === $n->nodeName) {
                $type->setLocationCodeableConcept(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_LOCATION_ADDRESS === $n->nodeName) {
                $type->setLocationAddress(FHIRAddress::xmlUnserialize($n));
            } elseif (self::FIELD_LOCATION_REFERENCE === $n->nodeName) {
                $type->setLocationReference(FHIRReference::xmlUnserialize($n));
            } elseif (self::FIELD_QUANTITY === $n->nodeName) {
                $type->setQuantity(FHIRQuantity::xmlUnserialize($n));
            } elseif (self::FIELD_UNIT_PRICE === $n->nodeName) {
                $type->setUnitPrice(FHIRMoney::xmlUnserialize($n));
            } elseif (self::FIELD_FACTOR === $n->nodeName) {
                $type->setFactor(FHIRDecimal::xmlUnserialize($n));
            } elseif (self::FIELD_NET === $n->nodeName) {
                $type->setNet(FHIRMoney::xmlUnserialize($n));
            } elseif (self::FIELD_UDI === $n->nodeName) {
                $type->addUdi(FHIRReference::xmlUnserialize($n));
            } elseif (self::FIELD_BODY_SITE === $n->nodeName) {
                $type->setBodySite(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_SUB_SITE === $n->nodeName) {
                $type->addSubSite(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_ENCOUNTER === $n->nodeName) {
                $type->addEncounter(FHIRReference::xmlUnserialize($n));
            } elseif (self::FIELD_NOTE_NUMBER === $n->nodeName) {
                $type->addNoteNumber(FHIRPositiveInt::xmlUnserialize($n));
            } elseif (self::FIELD_ADJUDICATION === $n->nodeName) {
                $type->addAdjudication(FHIRExplanationOfBenefitAdjudication::xmlUnserialize($n));
            } elseif (self::FIELD_DETAIL === $n->nodeName) {
                $type->addDetail(FHIRExplanationOfBenefitDetail::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER_EXTENSION === $n->nodeName) {
                $type->addModifierExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_EXTENSION === $n->nodeName) {
                $type->addExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_ID === $n->nodeName) {
                $type->setId(FHIRStringPrimitive::xmlUnserialize($n));
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_SEQUENCE);
        if (null !== $n) {
            $pt = $type->getSequence();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setSequence($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_CARE_TEAM_LINK_ID);
        if (null !== $n) {
            $pt = $type->getCareTeamLinkId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addCareTeamLinkId($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_DIAGNOSIS_LINK_ID);
        if (null !== $n) {
            $pt = $type->getDiagnosisLinkId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addDiagnosisLinkId($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_PROCEDURE_LINK_ID);
        if (null !== $n) {
            $pt = $type->getProcedureLinkId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addProcedureLinkId($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_INFORMATION_LINK_ID);
        if (null !== $n) {
            $pt = $type->getInformationLinkId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addInformationLinkId($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_SERVICED_DATE);
        if (null !== $n) {
            $pt = $type->getServicedDate();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setServicedDate($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_FACTOR);
        if (null !== $n) {
            $pt = $type->getFactor();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setFactor($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_NOTE_NUMBER);
        if (null !== $n) {
            $pt = $type->getNoteNumber();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addNoteNumber($n->nodeValue);
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
        if (null !== ($v = $this->getSequence())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_SEQUENCE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getCareTeamLinkId())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_CARE_TEAM_LINK_ID);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getDiagnosisLinkId())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_DIAGNOSIS_LINK_ID);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getProcedureLinkId())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_PROCEDURE_LINK_ID);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getInformationLinkId())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_INFORMATION_LINK_ID);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if (null !== ($v = $this->getRevenue())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_REVENUE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getCategory())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_CATEGORY);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getService())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_SERVICE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getModifier())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_MODIFIER);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getProgramCode())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_PROGRAM_CODE);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if (null !== ($v = $this->getServicedDate())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_SERVICED_DATE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getServicedPeriod())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_SERVICED_PERIOD);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getLocationCodeableConcept())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_LOCATION_CODEABLE_CONCEPT);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getLocationAddress())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_LOCATION_ADDRESS);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getLocationReference())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_LOCATION_REFERENCE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getQuantity())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_QUANTITY);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getUnitPrice())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_UNIT_PRICE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getFactor())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_FACTOR);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getNet())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_NET);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getUdi())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_UDI);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if (null !== ($v = $this->getBodySite())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_BODY_SITE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getSubSite())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_SUB_SITE);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getEncounter())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_ENCOUNTER);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getNoteNumber())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_NOTE_NUMBER);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getAdjudication())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_ADJUDICATION);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if ([] !== ($vs = $this->getDetail())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_DETAIL);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        return $element;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $a = parent::jsonSerialize();
        if (null !== ($v = $this->getSequence())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_SEQUENCE] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRPositiveInt::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_SEQUENCE_EXT] = $ext;
            }
        }
        if ([] !== ($vs = $this->getCareTeamLinkId())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRPositiveInt::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_CARE_TEAM_LINK_ID] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_CARE_TEAM_LINK_ID_EXT] = $exts;
            }
        }
        if ([] !== ($vs = $this->getDiagnosisLinkId())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRPositiveInt::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_DIAGNOSIS_LINK_ID] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_DIAGNOSIS_LINK_ID_EXT] = $exts;
            }
        }
        if ([] !== ($vs = $this->getProcedureLinkId())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRPositiveInt::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_PROCEDURE_LINK_ID] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_PROCEDURE_LINK_ID_EXT] = $exts;
            }
        }
        if ([] !== ($vs = $this->getInformationLinkId())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRPositiveInt::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_INFORMATION_LINK_ID] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_INFORMATION_LINK_ID_EXT] = $exts;
            }
        }
        if (null !== ($v = $this->getRevenue())) {
            $a[self::FIELD_REVENUE] = $v;
        }
        if (null !== ($v = $this->getCategory())) {
            $a[self::FIELD_CATEGORY] = $v;
        }
        if (null !== ($v = $this->getService())) {
            $a[self::FIELD_SERVICE] = $v;
        }
        if ([] !== ($vs = $this->getModifier())) {
            $a[self::FIELD_MODIFIER] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_MODIFIER][] = $v;
            }
        }
        if ([] !== ($vs = $this->getProgramCode())) {
            $a[self::FIELD_PROGRAM_CODE] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_PROGRAM_CODE][] = $v;
            }
        }
        if (null !== ($v = $this->getServicedDate())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_SERVICED_DATE] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRDate::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_SERVICED_DATE_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getServicedPeriod())) {
            $a[self::FIELD_SERVICED_PERIOD] = $v;
        }
        if (null !== ($v = $this->getLocationCodeableConcept())) {
            $a[self::FIELD_LOCATION_CODEABLE_CONCEPT] = $v;
        }
        if (null !== ($v = $this->getLocationAddress())) {
            $a[self::FIELD_LOCATION_ADDRESS] = $v;
        }
        if (null !== ($v = $this->getLocationReference())) {
            $a[self::FIELD_LOCATION_REFERENCE] = $v;
        }
        if (null !== ($v = $this->getQuantity())) {
            $a[self::FIELD_QUANTITY] = $v;
        }
        if (null !== ($v = $this->getUnitPrice())) {
            $a[self::FIELD_UNIT_PRICE] = $v;
        }
        if (null !== ($v = $this->getFactor())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_FACTOR] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRDecimal::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_FACTOR_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getNet())) {
            $a[self::FIELD_NET] = $v;
        }
        if ([] !== ($vs = $this->getUdi())) {
            $a[self::FIELD_UDI] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_UDI][] = $v;
            }
        }
        if (null !== ($v = $this->getBodySite())) {
            $a[self::FIELD_BODY_SITE] = $v;
        }
        if ([] !== ($vs = $this->getSubSite())) {
            $a[self::FIELD_SUB_SITE] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_SUB_SITE][] = $v;
            }
        }
        if ([] !== ($vs = $this->getEncounter())) {
            $a[self::FIELD_ENCOUNTER] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_ENCOUNTER][] = $v;
            }
        }
        if ([] !== ($vs = $this->getNoteNumber())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRPositiveInt::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_NOTE_NUMBER] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_NOTE_NUMBER_EXT] = $exts;
            }
        }
        if ([] !== ($vs = $this->getAdjudication())) {
            $a[self::FIELD_ADJUDICATION] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_ADJUDICATION][] = $v;
            }
        }
        if ([] !== ($vs = $this->getDetail())) {
            $a[self::FIELD_DETAIL] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_DETAIL][] = $v;
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