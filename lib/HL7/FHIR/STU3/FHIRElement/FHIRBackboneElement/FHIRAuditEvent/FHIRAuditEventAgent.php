<?php

namespace HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent;

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
use HL7\FHIR\STU3\FHIRElement\FHIRBoolean;
use HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept;
use HL7\FHIR\STU3\FHIRElement\FHIRCoding;
use HL7\FHIR\STU3\FHIRElement\FHIRExtension;
use HL7\FHIR\STU3\FHIRElement\FHIRIdentifier;
use HL7\FHIR\STU3\FHIRElement\FHIRReference;
use HL7\FHIR\STU3\FHIRElement\FHIRString;
use HL7\FHIR\STU3\FHIRElement\FHIRUri;
use HL7\FHIR\STU3\FHIRStringPrimitive;
use HL7\FHIR\STU3\PHPFHIRConstants;
use HL7\FHIR\STU3\PHPFHIRTypeInterface;

/**
 * A record of an event made for purposes of maintaining a security log. Typical
 * uses include detection of intrusion attempts and monitoring for inappropriate
 * usage.
 *
 * Class FHIRAuditEventAgent
 * @package \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent
 */
class FHIRAuditEventAgent extends FHIRBackboneElement
{
    // name of FHIR type this class describes
    const FHIR_TYPE_NAME = PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT;
    const FIELD_ROLE = 'role';
    const FIELD_REFERENCE = 'reference';
    const FIELD_USER_ID = 'userId';
    const FIELD_ALT_ID = 'altId';
    const FIELD_ALT_ID_EXT = '_altId';
    const FIELD_NAME = 'name';
    const FIELD_NAME_EXT = '_name';
    const FIELD_REQUESTOR = 'requestor';
    const FIELD_REQUESTOR_EXT = '_requestor';
    const FIELD_LOCATION = 'location';
    const FIELD_POLICY = 'policy';
    const FIELD_POLICY_EXT = '_policy';
    const FIELD_MEDIA = 'media';
    const FIELD_NETWORK = 'network';
    const FIELD_PURPOSE_OF_USE = 'purposeOfUse';

    /** @var string */
    private $_xmlns = '';

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The security role that the user was acting under, that come from local codes
     * defined by the access control security system (e.g. RBAC, ABAC) used in the
     * local context.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    protected $role = [];

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Direct reference to a resource that identifies the agent.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    protected $reference = null;

    /**
     * A technical identifier - identifies some entity uniquely and unambiguously.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Unique identifier for the user actively participating in the event.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRIdentifier
     */
    protected $userId = null;

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Alternative agent Identifier. For a human, this should be a user identifier text
     * string from authentication system. This identifier would be one known to a
     * common authentication system (e.g. single sign-on), if available.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    protected $altId = null;

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Human-meaningful name for the agent.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    protected $name = null;

    /**
     * Value of "true" or "false"
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Indicator that the user is or is not the requestor, or initiator, for the event
     * being audited.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRBoolean
     */
    protected $requestor = null;

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the event occurred.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    protected $location = null;

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The policy or plan that authorized the activity being recorded. Typically, a
     * single activity may have multiple applicable policies, such as patient consent,
     * guarantor funding, etc. The policy would also indicate the security token used.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRUri[]
     */
    protected $policy = [];

    /**
     * A reference to a code defined by a terminology system.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Type of media involved. Used when the event is about exporting/importing onto
     * media.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCoding
     */
    protected $media = null;

    /**
     * A record of an event made for purposes of maintaining a security log. Typical
     * uses include detection of intrusion attempts and monitoring for inappropriate
     * usage.
     *
     * Logical network location for application activity, if the activity has a network
     * location.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventNetwork
     */
    protected $network = null;

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The reason (purpose of use), specific to this agent, that was used during the
     * event being recorded.
     *
     * @var null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    protected $purposeOfUse = [];

    /**
     * Validation map for fields in type AuditEvent.Agent
     * @var array
     */
    private static $_validationRules = [    ];

    /**
     * FHIRAuditEventAgent Constructor
     * @param null|array $data
     */
    public function __construct($data = null)
    {
        if (null === $data || [] === $data) {
            return;
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'FHIRAuditEventAgent::_construct - $data expected to be null or array, %s seen',
                gettype($data)
            ));
        }
        parent::__construct($data);
        if (isset($data[self::FIELD_ROLE])) {
            if (is_array($data[self::FIELD_ROLE])) {
                foreach($data[self::FIELD_ROLE] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRCodeableConcept) {
                        $this->addRole($v);
                    } else {
                        $this->addRole(new FHIRCodeableConcept($v));
                    }
                }
            } elseif ($data[self::FIELD_ROLE] instanceof FHIRCodeableConcept) {
                $this->addRole($data[self::FIELD_ROLE]);
            } else {
                $this->addRole(new FHIRCodeableConcept($data[self::FIELD_ROLE]));
            }
        }
        if (isset($data[self::FIELD_REFERENCE])) {
            if ($data[self::FIELD_REFERENCE] instanceof FHIRReference) {
                $this->setReference($data[self::FIELD_REFERENCE]);
            } else {
                $this->setReference(new FHIRReference($data[self::FIELD_REFERENCE]));
            }
        }
        if (isset($data[self::FIELD_USER_ID])) {
            if ($data[self::FIELD_USER_ID] instanceof FHIRIdentifier) {
                $this->setUserId($data[self::FIELD_USER_ID]);
            } else {
                $this->setUserId(new FHIRIdentifier($data[self::FIELD_USER_ID]));
            }
        }
        if (isset($data[self::FIELD_ALT_ID]) || isset($data[self::FIELD_ALT_ID_EXT])) {
            $value = isset($data[self::FIELD_ALT_ID]) ? $data[self::FIELD_ALT_ID] : null;
            $ext = (isset($data[self::FIELD_ALT_ID_EXT]) && is_array($data[self::FIELD_ALT_ID_EXT])) ? $ext = $data[self::FIELD_ALT_ID_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRString) {
                    $this->setAltId($value);
                } else if (is_array($value)) {
                    $this->setAltId(new FHIRString(array_merge($ext, $value)));
                } else {
                    $this->setAltId(new FHIRString([FHIRString::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setAltId(new FHIRString($ext));
            }
        }
        if (isset($data[self::FIELD_NAME]) || isset($data[self::FIELD_NAME_EXT])) {
            $value = isset($data[self::FIELD_NAME]) ? $data[self::FIELD_NAME] : null;
            $ext = (isset($data[self::FIELD_NAME_EXT]) && is_array($data[self::FIELD_NAME_EXT])) ? $ext = $data[self::FIELD_NAME_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRString) {
                    $this->setName($value);
                } else if (is_array($value)) {
                    $this->setName(new FHIRString(array_merge($ext, $value)));
                } else {
                    $this->setName(new FHIRString([FHIRString::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setName(new FHIRString($ext));
            }
        }
        if (isset($data[self::FIELD_REQUESTOR]) || isset($data[self::FIELD_REQUESTOR_EXT])) {
            $value = isset($data[self::FIELD_REQUESTOR]) ? $data[self::FIELD_REQUESTOR] : null;
            $ext = (isset($data[self::FIELD_REQUESTOR_EXT]) && is_array($data[self::FIELD_REQUESTOR_EXT])) ? $ext = $data[self::FIELD_REQUESTOR_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRBoolean) {
                    $this->setRequestor($value);
                } else if (is_array($value)) {
                    $this->setRequestor(new FHIRBoolean(array_merge($ext, $value)));
                } else {
                    $this->setRequestor(new FHIRBoolean([FHIRBoolean::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                $this->setRequestor(new FHIRBoolean($ext));
            }
        }
        if (isset($data[self::FIELD_LOCATION])) {
            if ($data[self::FIELD_LOCATION] instanceof FHIRReference) {
                $this->setLocation($data[self::FIELD_LOCATION]);
            } else {
                $this->setLocation(new FHIRReference($data[self::FIELD_LOCATION]));
            }
        }
        if (isset($data[self::FIELD_POLICY]) || isset($data[self::FIELD_POLICY_EXT])) {
            $value = isset($data[self::FIELD_POLICY]) ? $data[self::FIELD_POLICY] : null;
            $ext = (isset($data[self::FIELD_POLICY_EXT]) && is_array($data[self::FIELD_POLICY_EXT])) ? $ext = $data[self::FIELD_POLICY_EXT] : $ext = [];
            if (null !== $value) {
                if ($value instanceof FHIRUri) {
                    $this->addPolicy($value);
                } else if (is_array($value)) {
                    foreach($value as $i => $v) {
                        if ($v instanceof FHIRUri) {
                            $this->addPolicy($v);
                        } else {
                            $iext = (isset($ext[$i]) && is_array($ext[$i])) ? $ext[$i] : [];
                            if (is_array($v)) {
                                $this->addPolicy(new FHIRUri(array_merge($v, $iext)));
                            } else {
                                $this->addPolicy(new FHIRUri([FHIRUri::FIELD_VALUE => $v] + $iext));
                            }
                        }
                    }
                } elseif (is_array($value)) {
                    $this->addPolicy(new FHIRUri(array_merge($ext, $value)));
                } else {
                    $this->addPolicy(new FHIRUri([FHIRUri::FIELD_VALUE => $value] + $ext));
                }
            } elseif ([] !== $ext) {
                foreach($ext as $iext) {
                    $this->addPolicy(new FHIRUri($iext));
                }
            }
        }
        if (isset($data[self::FIELD_MEDIA])) {
            if ($data[self::FIELD_MEDIA] instanceof FHIRCoding) {
                $this->setMedia($data[self::FIELD_MEDIA]);
            } else {
                $this->setMedia(new FHIRCoding($data[self::FIELD_MEDIA]));
            }
        }
        if (isset($data[self::FIELD_NETWORK])) {
            if ($data[self::FIELD_NETWORK] instanceof FHIRAuditEventNetwork) {
                $this->setNetwork($data[self::FIELD_NETWORK]);
            } else {
                $this->setNetwork(new FHIRAuditEventNetwork($data[self::FIELD_NETWORK]));
            }
        }
        if (isset($data[self::FIELD_PURPOSE_OF_USE])) {
            if (is_array($data[self::FIELD_PURPOSE_OF_USE])) {
                foreach($data[self::FIELD_PURPOSE_OF_USE] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if ($v instanceof FHIRCodeableConcept) {
                        $this->addPurposeOfUse($v);
                    } else {
                        $this->addPurposeOfUse(new FHIRCodeableConcept($v));
                    }
                }
            } elseif ($data[self::FIELD_PURPOSE_OF_USE] instanceof FHIRCodeableConcept) {
                $this->addPurposeOfUse($data[self::FIELD_PURPOSE_OF_USE]);
            } else {
                $this->addPurposeOfUse(new FHIRCodeableConcept($data[self::FIELD_PURPOSE_OF_USE]));
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
        return "<AuditEventAgent{$xmlns}></AuditEventAgent>";
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The security role that the user was acting under, that come from local codes
     * defined by the access control security system (e.g. RBAC, ABAC) used in the
     * local context.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The security role that the user was acting under, that come from local codes
     * defined by the access control security system (e.g. RBAC, ABAC) used in the
     * local context.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $role
     * @return static
     */
    public function addRole(FHIRCodeableConcept $role = null)
    {
        $this->_trackValueAdded();
        $this->role[] = $role;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The security role that the user was acting under, that come from local codes
     * defined by the access control security system (e.g. RBAC, ABAC) used in the
     * local context.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[] $role
     * @return static
     */
    public function setRole(array $role = [])
    {
        if ([] !== $this->role) {
            $this->_trackValuesRemoved(count($this->role));
            $this->role = [];
        }
        if ([] === $role) {
            return $this;
        }
        foreach($role as $v) {
            if ($v instanceof FHIRCodeableConcept) {
                $this->addRole($v);
            } else {
                $this->addRole(new FHIRCodeableConcept($v));
            }
        }
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Direct reference to a resource that identifies the agent.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Direct reference to a resource that identifies the agent.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRReference $reference
     * @return static
     */
    public function setReference(FHIRReference $reference = null)
    {
        $this->_trackValueSet($this->reference, $reference);
        $this->reference = $reference;
        return $this;
    }

    /**
     * A technical identifier - identifies some entity uniquely and unambiguously.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Unique identifier for the user actively participating in the event.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRIdentifier
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * A technical identifier - identifies some entity uniquely and unambiguously.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Unique identifier for the user actively participating in the event.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRIdentifier $userId
     * @return static
     */
    public function setUserId(FHIRIdentifier $userId = null)
    {
        $this->_trackValueSet($this->userId, $userId);
        $this->userId = $userId;
        return $this;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Alternative agent Identifier. For a human, this should be a user identifier text
     * string from authentication system. This identifier would be one known to a
     * common authentication system (e.g. single sign-on), if available.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    public function getAltId()
    {
        return $this->altId;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Alternative agent Identifier. For a human, this should be a user identifier text
     * string from authentication system. This identifier would be one known to a
     * common authentication system (e.g. single sign-on), if available.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRString $altId
     * @return static
     */
    public function setAltId($altId = null)
    {
        if (null !== $altId && !($altId instanceof FHIRString)) {
            $altId = new FHIRString($altId);
        }
        $this->_trackValueSet($this->altId, $altId);
        $this->altId = $altId;
        return $this;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Human-meaningful name for the agent.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRString
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * A sequence of Unicode characters
     * Note that FHIR strings may not exceed 1MB in size
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Human-meaningful name for the agent.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRString $name
     * @return static
     */
    public function setName($name = null)
    {
        if (null !== $name && !($name instanceof FHIRString)) {
            $name = new FHIRString($name);
        }
        $this->_trackValueSet($this->name, $name);
        $this->name = $name;
        return $this;
    }

    /**
     * Value of "true" or "false"
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Indicator that the user is or is not the requestor, or initiator, for the event
     * being audited.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBoolean
     */
    public function getRequestor()
    {
        return $this->requestor;
    }

    /**
     * Value of "true" or "false"
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * Indicator that the user is or is not the requestor, or initiator, for the event
     * being audited.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBoolean $requestor
     * @return static
     */
    public function setRequestor($requestor = null)
    {
        if (null !== $requestor && !($requestor instanceof FHIRBoolean)) {
            $requestor = new FHIRBoolean($requestor);
        }
        $this->_trackValueSet($this->requestor, $requestor);
        $this->requestor = $requestor;
        return $this;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the event occurred.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRReference
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * A reference from one resource to another.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Where the event occurred.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRReference $location
     * @return static
     */
    public function setLocation(FHIRReference $location = null)
    {
        $this->_trackValueSet($this->location, $location);
        $this->location = $location;
        return $this;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The policy or plan that authorized the activity being recorded. Typically, a
     * single activity may have multiple applicable policies, such as patient consent,
     * guarantor funding, etc. The policy would also indicate the security token used.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRUri[]
     */
    public function getPolicy()
    {
        return $this->policy;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The policy or plan that authorized the activity being recorded. Typically, a
     * single activity may have multiple applicable policies, such as patient consent,
     * guarantor funding, etc. The policy would also indicate the security token used.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRUri $policy
     * @return static
     */
    public function addPolicy($policy = null)
    {
        if (null !== $policy && !($policy instanceof FHIRUri)) {
            $policy = new FHIRUri($policy);
        }
        $this->_trackValueAdded();
        $this->policy[] = $policy;
        return $this;
    }

    /**
     * String of characters used to identify a name or a resource
     * see http://en.wikipedia.org/wiki/Uniform_resource_identifier
     * If the element is present, it must have either a \@value, an \@id, or extensions
     *
     * The policy or plan that authorized the activity being recorded. Typically, a
     * single activity may have multiple applicable policies, such as patient consent,
     * guarantor funding, etc. The policy would also indicate the security token used.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRUri[] $policy
     * @return static
     */
    public function setPolicy(array $policy = [])
    {
        if ([] !== $this->policy) {
            $this->_trackValuesRemoved(count($this->policy));
            $this->policy = [];
        }
        if ([] === $policy) {
            return $this;
        }
        foreach($policy as $v) {
            if ($v instanceof FHIRUri) {
                $this->addPolicy($v);
            } else {
                $this->addPolicy(new FHIRUri($v));
            }
        }
        return $this;
    }

    /**
     * A reference to a code defined by a terminology system.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Type of media involved. Used when the event is about exporting/importing onto
     * media.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCoding
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * A reference to a code defined by a terminology system.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * Type of media involved. Used when the event is about exporting/importing onto
     * media.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCoding $media
     * @return static
     */
    public function setMedia(FHIRCoding $media = null)
    {
        $this->_trackValueSet($this->media, $media);
        $this->media = $media;
        return $this;
    }

    /**
     * A record of an event made for purposes of maintaining a security log. Typical
     * uses include detection of intrusion attempts and monitoring for inappropriate
     * usage.
     *
     * Logical network location for application activity, if the activity has a network
     * location.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventNetwork
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * A record of an event made for purposes of maintaining a security log. Typical
     * uses include detection of intrusion attempts and monitoring for inappropriate
     * usage.
     *
     * Logical network location for application activity, if the activity has a network
     * location.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventNetwork $network
     * @return static
     */
    public function setNetwork(FHIRAuditEventNetwork $network = null)
    {
        $this->_trackValueSet($this->network, $network);
        $this->network = $network;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The reason (purpose of use), specific to this agent, that was used during the
     * event being recorded.
     *
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[]
     */
    public function getPurposeOfUse()
    {
        return $this->purposeOfUse;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The reason (purpose of use), specific to this agent, that was used during the
     * event being recorded.
     *
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept $purposeOfUse
     * @return static
     */
    public function addPurposeOfUse(FHIRCodeableConcept $purposeOfUse = null)
    {
        $this->_trackValueAdded();
        $this->purposeOfUse[] = $purposeOfUse;
        return $this;
    }

    /**
     * A concept that may be defined by a formal reference to a terminology or ontology
     * or may be provided by text.
     * If the element is present, it must have a value for at least one of the defined
     * elements, an \@id referenced from the Narrative, or extensions
     *
     * The reason (purpose of use), specific to this agent, that was used during the
     * event being recorded.
     *
     * @param \HL7\FHIR\STU3\FHIRElement\FHIRCodeableConcept[] $purposeOfUse
     * @return static
     */
    public function setPurposeOfUse(array $purposeOfUse = [])
    {
        if ([] !== $this->purposeOfUse) {
            $this->_trackValuesRemoved(count($this->purposeOfUse));
            $this->purposeOfUse = [];
        }
        if ([] === $purposeOfUse) {
            return $this;
        }
        foreach($purposeOfUse as $v) {
            if ($v instanceof FHIRCodeableConcept) {
                $this->addPurposeOfUse($v);
            } else {
                $this->addPurposeOfUse(new FHIRCodeableConcept($v));
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
        if ([] !== ($vs = $this->getRole())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_ROLE, $i)] = $fieldErrs;
                }
            }
        }
        if (null !== ($v = $this->getReference())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_REFERENCE] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getUserId())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_USER_ID] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getAltId())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_ALT_ID] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getName())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_NAME] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getRequestor())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_REQUESTOR] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getLocation())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_LOCATION] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getPolicy())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_POLICY, $i)] = $fieldErrs;
                }
            }
        }
        if (null !== ($v = $this->getMedia())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_MEDIA] = $fieldErrs;
            }
        }
        if (null !== ($v = $this->getNetwork())) {
            if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                $errs[self::FIELD_NETWORK] = $fieldErrs;
            }
        }
        if ([] !== ($vs = $this->getPurposeOfUse())) {
            foreach($vs as $i => $v) {
                if ([] !== ($fieldErrs = $v->_getValidationErrors())) {
                    $errs[sprintf('%s.%d', self::FIELD_PURPOSE_OF_USE, $i)] = $fieldErrs;
                }
            }
        }
        if (isset($validationRules[self::FIELD_ROLE])) {
            $v = $this->getRole();
            foreach($validationRules[self::FIELD_ROLE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_ROLE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_ROLE])) {
                        $errs[self::FIELD_ROLE] = [];
                    }
                    $errs[self::FIELD_ROLE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_REFERENCE])) {
            $v = $this->getReference();
            foreach($validationRules[self::FIELD_REFERENCE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_REFERENCE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_REFERENCE])) {
                        $errs[self::FIELD_REFERENCE] = [];
                    }
                    $errs[self::FIELD_REFERENCE][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_USER_ID])) {
            $v = $this->getUserId();
            foreach($validationRules[self::FIELD_USER_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_USER_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_USER_ID])) {
                        $errs[self::FIELD_USER_ID] = [];
                    }
                    $errs[self::FIELD_USER_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_ALT_ID])) {
            $v = $this->getAltId();
            foreach($validationRules[self::FIELD_ALT_ID] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_ALT_ID, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_ALT_ID])) {
                        $errs[self::FIELD_ALT_ID] = [];
                    }
                    $errs[self::FIELD_ALT_ID][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_NAME])) {
            $v = $this->getName();
            foreach($validationRules[self::FIELD_NAME] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_NAME, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_NAME])) {
                        $errs[self::FIELD_NAME] = [];
                    }
                    $errs[self::FIELD_NAME][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_REQUESTOR])) {
            $v = $this->getRequestor();
            foreach($validationRules[self::FIELD_REQUESTOR] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_REQUESTOR, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_REQUESTOR])) {
                        $errs[self::FIELD_REQUESTOR] = [];
                    }
                    $errs[self::FIELD_REQUESTOR][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_LOCATION])) {
            $v = $this->getLocation();
            foreach($validationRules[self::FIELD_LOCATION] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_LOCATION, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_LOCATION])) {
                        $errs[self::FIELD_LOCATION] = [];
                    }
                    $errs[self::FIELD_LOCATION][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_POLICY])) {
            $v = $this->getPolicy();
            foreach($validationRules[self::FIELD_POLICY] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_POLICY, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_POLICY])) {
                        $errs[self::FIELD_POLICY] = [];
                    }
                    $errs[self::FIELD_POLICY][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_MEDIA])) {
            $v = $this->getMedia();
            foreach($validationRules[self::FIELD_MEDIA] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_MEDIA, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_MEDIA])) {
                        $errs[self::FIELD_MEDIA] = [];
                    }
                    $errs[self::FIELD_MEDIA][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_NETWORK])) {
            $v = $this->getNetwork();
            foreach($validationRules[self::FIELD_NETWORK] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_NETWORK, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_NETWORK])) {
                        $errs[self::FIELD_NETWORK] = [];
                    }
                    $errs[self::FIELD_NETWORK][$rule] = $err;
                }
            }
        }
        if (isset($validationRules[self::FIELD_PURPOSE_OF_USE])) {
            $v = $this->getPurposeOfUse();
            foreach($validationRules[self::FIELD_PURPOSE_OF_USE] as $rule => $constraint) {
                $err = $this->_performValidation(PHPFHIRConstants::TYPE_NAME_AUDIT_EVENT_DOT_AGENT, self::FIELD_PURPOSE_OF_USE, $rule, $constraint, $v);
                if (null !== $err) {
                    if (!isset($errs[self::FIELD_PURPOSE_OF_USE])) {
                        $errs[self::FIELD_PURPOSE_OF_USE] = [];
                    }
                    $errs[self::FIELD_PURPOSE_OF_USE][$rule] = $err;
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
     * @param null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventAgent $type
     * @param null|int $libxmlOpts
     * @return null|\HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventAgent
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
                throw new \DomainException(sprintf('FHIRAuditEventAgent::xmlUnserialize - String provided is not parseable as XML: %s', implode(', ', array_map(function(\libXMLError $err) { return $err->message; }, libxml_get_errors()))));
            }
            libxml_use_internal_errors(false);
            $element = $dom->documentElement;
        }
        if (!($element instanceof \DOMElement)) {
            throw new \InvalidArgumentException(sprintf('FHIRAuditEventAgent::xmlUnserialize - $node value must be null, \\DOMElement, or valid XML string, %s seen', is_object($element) ? get_class($element) : gettype($element)));
        }
        if (null === $type) {
            $type = new FHIRAuditEventAgent(null);
        } elseif (!is_object($type) || !($type instanceof FHIRAuditEventAgent)) {
            throw new \RuntimeException(sprintf(
                'FHIRAuditEventAgent::xmlUnserialize - $type must be instance of \HL7\FHIR\STU3\FHIRElement\FHIRBackboneElement\FHIRAuditEvent\FHIRAuditEventAgent or null, %s seen.',
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
            if (self::FIELD_ROLE === $n->nodeName) {
                $type->addRole(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_REFERENCE === $n->nodeName) {
                $type->setReference(FHIRReference::xmlUnserialize($n));
            } elseif (self::FIELD_USER_ID === $n->nodeName) {
                $type->setUserId(FHIRIdentifier::xmlUnserialize($n));
            } elseif (self::FIELD_ALT_ID === $n->nodeName) {
                $type->setAltId(FHIRString::xmlUnserialize($n));
            } elseif (self::FIELD_NAME === $n->nodeName) {
                $type->setName(FHIRString::xmlUnserialize($n));
            } elseif (self::FIELD_REQUESTOR === $n->nodeName) {
                $type->setRequestor(FHIRBoolean::xmlUnserialize($n));
            } elseif (self::FIELD_LOCATION === $n->nodeName) {
                $type->setLocation(FHIRReference::xmlUnserialize($n));
            } elseif (self::FIELD_POLICY === $n->nodeName) {
                $type->addPolicy(FHIRUri::xmlUnserialize($n));
            } elseif (self::FIELD_MEDIA === $n->nodeName) {
                $type->setMedia(FHIRCoding::xmlUnserialize($n));
            } elseif (self::FIELD_NETWORK === $n->nodeName) {
                $type->setNetwork(FHIRAuditEventNetwork::xmlUnserialize($n));
            } elseif (self::FIELD_PURPOSE_OF_USE === $n->nodeName) {
                $type->addPurposeOfUse(FHIRCodeableConcept::xmlUnserialize($n));
            } elseif (self::FIELD_MODIFIER_EXTENSION === $n->nodeName) {
                $type->addModifierExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_EXTENSION === $n->nodeName) {
                $type->addExtension(FHIRExtension::xmlUnserialize($n));
            } elseif (self::FIELD_ID === $n->nodeName) {
                $type->setId(FHIRStringPrimitive::xmlUnserialize($n));
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_ALT_ID);
        if (null !== $n) {
            $pt = $type->getAltId();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setAltId($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_NAME);
        if (null !== $n) {
            $pt = $type->getName();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setName($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_REQUESTOR);
        if (null !== $n) {
            $pt = $type->getRequestor();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->setRequestor($n->nodeValue);
            }
        }
        $n = $element->attributes->getNamedItem(self::FIELD_POLICY);
        if (null !== $n) {
            $pt = $type->getPolicy();
            if (null !== $pt) {
                $pt->setValue($n->nodeValue);
            } else {
                $type->addPolicy($n->nodeValue);
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
        if ([] !== ($vs = $this->getRole())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_ROLE);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if (null !== ($v = $this->getReference())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_REFERENCE);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getUserId())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_USER_ID);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getAltId())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_ALT_ID);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getName())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_NAME);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getRequestor())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_REQUESTOR);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getLocation())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_LOCATION);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getPolicy())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_POLICY);
                $element->appendChild($telement);
                $v->xmlSerialize($telement);
            }
        }
        if (null !== ($v = $this->getMedia())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_MEDIA);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if (null !== ($v = $this->getNetwork())) {
            $telement = $element->ownerDocument->createElement(self::FIELD_NETWORK);
            $element->appendChild($telement);
            $v->xmlSerialize($telement);
        }
        if ([] !== ($vs = $this->getPurposeOfUse())) {
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $telement = $element->ownerDocument->createElement(self::FIELD_PURPOSE_OF_USE);
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
        if ([] !== ($vs = $this->getRole())) {
            $a[self::FIELD_ROLE] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_ROLE][] = $v;
            }
        }
        if (null !== ($v = $this->getReference())) {
            $a[self::FIELD_REFERENCE] = $v;
        }
        if (null !== ($v = $this->getUserId())) {
            $a[self::FIELD_USER_ID] = $v;
        }
        if (null !== ($v = $this->getAltId())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_ALT_ID] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRString::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_ALT_ID_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getName())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_NAME] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRString::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_NAME_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getRequestor())) {
            if (null !== ($val = $v->getValue())) {
                $a[self::FIELD_REQUESTOR] = $val;
            }
            $ext = $v->jsonSerialize();
            unset($ext[FHIRBoolean::FIELD_VALUE]);
            if ([] !== $ext) {
                $a[self::FIELD_REQUESTOR_EXT] = $ext;
            }
        }
        if (null !== ($v = $this->getLocation())) {
            $a[self::FIELD_LOCATION] = $v;
        }
        if ([] !== ($vs = $this->getPolicy())) {
            $vals = [];
            $exts = [];
            foreach ($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $val = $v->getValue();
                $ext = $v->jsonSerialize();
                unset($ext[FHIRUri::FIELD_VALUE]);
                if (null !== $val) {
                    $vals[] = $val;
                }
                if ([] !== $ext) {
                    $exts[] = $ext;
                }
            }
            if ([] !== $vals) {
                $a[self::FIELD_POLICY] = $vals;
            }
            if ([] !== $exts) {
                $a[self::FIELD_POLICY_EXT] = $exts;
            }
        }
        if (null !== ($v = $this->getMedia())) {
            $a[self::FIELD_MEDIA] = $v;
        }
        if (null !== ($v = $this->getNetwork())) {
            $a[self::FIELD_NETWORK] = $v;
        }
        if ([] !== ($vs = $this->getPurposeOfUse())) {
            $a[self::FIELD_PURPOSE_OF_USE] = [];
            foreach($vs as $v) {
                if (null === $v) {
                    continue;
                }
                $a[self::FIELD_PURPOSE_OF_USE][] = $v;
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