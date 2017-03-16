<?php
/**
 * mdTimeLine EHR (Electronic Health Records)
 * Copyright (C) 2017 mdTimeLine, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.

if(!isset($_SESSION)){
    session_cache_limiter('private');
    session_name('mdTimeLine');
    session_start();
}

$site = isset($_SESSION['user']['site']) ? $_SESSION['user']['site'] : 'default';
if(!defined('_GaiaEXEC')) define('_GaiaEXEC', 1);
require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');

include_once(ROOT.'/dataProvider/CCDDocumentBase.php');

include_once(ROOT . '/classes/UUID.php');
include_once(ROOT . '/classes/Array2XML.php');

include_once(ROOT . '/dataProvider/Patient.php');
include_once(ROOT . '/dataProvider/PatientContacts.php');
include_once(ROOT . '/dataProvider/Insurance.php');
include_once(ROOT . '/dataProvider/User.php');
include_once(ROOT . '/dataProvider/Rxnorm.php');
include_once(ROOT . '/dataProvider/Encounter.php');
include_once(ROOT . '/dataProvider/PoolArea.php');
include_once(ROOT . '/dataProvider/Vitals.php');
include_once(ROOT . '/dataProvider/Immunizations.php');
include_once(ROOT . '/dataProvider/ActiveProblems.php');
include_once(ROOT . '/dataProvider/Allergies.php');
include_once(ROOT . '/dataProvider/Orders.php');
include_once(ROOT . '/dataProvider/Medications.php');
include_once(ROOT . '/dataProvider/CarePlanGoals.php');
include_once(ROOT . '/dataProvider/CognitiveAndFunctionalStatus.php');
include_once(ROOT . '/dataProvider/Procedures.php');
include_once(ROOT . '/dataProvider/SocialHistory.php');
include_once(ROOT . '/dataProvider/Services.php');
include_once(ROOT . '/dataProvider/Referrals.php');
include_once(ROOT . '/dataProvider/ReferringProviders.php');
include_once(ROOT . '/dataProvider/DiagnosisCodes.php');
include_once(ROOT . '/dataProvider/Facilities.php');
include_once(ROOT . '/dataProvider/CombosData.php');
include_once(ROOT . '/dataProvider/TransactionLog.php');
include_once(ROOT . '/dataProvider/AppointmentRequest.php');
include_once(ROOT . '/dataProvider/DecisionAids.php');
include_once(ROOT . '/dataProvider/Globals.php');

class CCDDocument extends CDDDocumentBase
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
     * Method setHeader()
     */
    public function setHeader()
    {
        $this->xmlData['realmCode'] = [
            '@attributes' => [
                'code' => 'US'
            ]
        ];
        $this->xmlData['typeId'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.1.3',
                'extension' => 'POCD_HD000040'
            ]
        ];
        // QRDA templateId
        $this->xmlData['templateId'][] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.1.1'
            ]
        ];
        // QDM-based QRDA templateId
        $this->xmlData['templateId'][] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.1.2'
            ]
        ];
        // QRDA templateId
        $this->xmlData['templateId'][] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.24.1.1'
            ]
        ];
        // QDM-based QRDA templateId
        $this->xmlData['templateId'][] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.24.1.2'
            ]
        ];
        $this->xmlData['id'] = [
            '@attributes' => [
                'root' => 'MDHT',
                'extension' => '1912668293'
            ]
        ];
        $this->xmlData['code'] = [
            '@attributes' => [
                'code' => '34133-9',
                'displayName' => 'Summary of episode note',
                'codeSystem' => '2.16.840.1.113883.6.1',
                'codeSystemName' => 'LOINC'
            ]
        ];

        if(isset($this->encounter)){
            $this->xmlData['title'] = $this->facility['name'] . ' - Clinical Office Visit Summary';
        }else{
            $this->xmlData['title'] = $this->facility['name'] . ' - Continuity of Care Document';
        }

        $this->xmlData['effectiveTime'] = [
            '@attributes' => [
                'value' => $this->timeNow
            ]
        ];
        $this->xmlData['confidentialityCode'] = [
            '@attributes' => [
                'code' => 'N',
                'codeSystem' => '2.16.840.1.113883.5.25'
            ]
        ];
        $this->xmlData['languageCode'] = [
            '@attributes' => [
                'code' => 'US'
            ]
        ];

        $this->patientData = $this->Patient->getPatientDemographicDataByPid($this->pid);

        // If the user is set in the session, this CDA request came from the EHR, if not
        // the request came from the Patient Portal (PHR)
        if(isset($_SESSION['user'])){
            $this->user = $this->User->getUserByUid($_SESSION['user']['id']);
        } else {
            $this->user['title'] = '';
            $this->user['fname'] = 'mdTimeLine';
            $this->user['lname'] = 'PHR';
            $this->user['npi'] = '';
            $this->user['id'] = 0;
        }

        $this->primaryProvider = $this->User->getUserByUid($this->patientData['primary_provider']);

        $this->xmlData['recordTarget'] = $this->getRecordTarget();
        $this->xmlData['author'] = $this->getAuthor();
        $this->xmlData['dataEnterer'] = $this->getDataEnterer();
        $this->xmlData['informant'] = $this->getInformant();
        $this->xmlData['custodian'] = $this->getCustodian();
        $this->xmlData['informationRecipient'] = $this->getInformationRecipient();
        $this->xmlData['legalAuthenticator'] = $this->getAuthenticator();
        $this->xmlData['authenticator'] = $this->getAuthenticator();
        $this->xmlData['documentationOf'] = $this->getDocumentationOf();

        if(isset($this->encounter)) $this->xmlData['componentOf'] = $this->getComponentOf();

        $this->xmlData['component']['structuredBody']['component'] = [];

    }

    /**
     * Method getRecordTarget()
     *
     * The recordTarget records the administrative and demographic data of the patient whose health information
     * is described by the clinical document; each recordTarget must contain at least one patientRole element
     *
     * @return array
     */
    public function getRecordTarget() {
        $patientData = $this->patientData;
        $Insurance = new Insurance();
        $insuranceData = $Insurance->getPatientPrimaryInsuranceByPid($this->pid);

        $excludePatient = $this->isExcluded('patient_information');

        $recordTarget['patientRole']['id'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.19.5',
                'extension' => $patientData['pid']
            ]
        ];

        if($excludePatient){
	        $recordTarget['patientRole']['addr'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['telecom'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['name'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['administrativeGenderCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['birthTime'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['maritalStatusCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['raceCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['ethnicGroupCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
	        $recordTarget['patientRole']['patient']['birthplace']['place']['addr'] = $this->addressBuilder(
		        false,
		        false,
		        false,
		        false,
		        false,
		        ''
	        );
	        $recordTarget['patientRole']['patient']['languageCommunication'] = [
	        	'languageCode' => [
			        '@attributes' => [
				        'nullFlavor' => 'UNK'
			        ]
		        ]
	        ];

	        return $recordTarget;

        }

        // If the Self Contact information address is set, include it in the CCD
        $recordTarget['patientRole']['addr'] = $this->addressBuilder(
            'HP',
            $patientData['postal_address'] . ' ' . $patientData['postal_address_cont'],
            $patientData['postal_city'],
            $patientData['postal_state'],
            $patientData['postal_zip'],
            $patientData['postal_country'],
            date('Ymd')
        );

        // If the Self Contact information phone is present, include it in the CCD
        if(isset($patientData['phone_home'])){
            $recordTarget['patientRole']['telecom'] = $this->telecomBuilder(
                $patientData['phone_home'],
                'HP'
            );
        }

        if($this->isExcluded('patient_name')){
	        $recordTarget['patientRole']['patient']['name'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        // Patient Name
	        $recordTarget['patientRole']['patient']['name'] = [
		        '@attributes' => [
			        'use' => 'L'
		        ],
	        ];

	        $recordTarget['patientRole']['patient']['name']['given'][] = $patientData['fname'];

	        if($patientData['mname'] != ''){
		        $recordTarget['patientRole']['patient']['name']['given'][] = $patientData['mname'];
	        }

	        $recordTarget['patientRole']['patient']['name']['family'] = $patientData['lname'];

	        if($patientData['title'] != ''){
		        $recordTarget['patientRole']['patient']['name']['suffix'] = [
			        '@attributes' => [
				        'qualifier' => 'TITLE'
			        ],
			        '@value' => isset($patientData['title']) ? $patientData['title'] : ''
		        ];
	        }
        }


        if($this->isExcluded('patient_sex')){
	        $recordTarget['patientRole']['patient']['administrativeGenderCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        // values are M, F, or UM more info...
	        // http://phinvads.cdc.gov/vads/ViewValueSet.action?id=8DE75E17-176B-DE11-9B52-0015173D1785
	        $recordTarget['patientRole']['patient']['administrativeGenderCode'] = [
		        '@attributes' => [
			        'code' => $patientData['sex'],
			        'codeSystemName' => 'AdministrativeGender',
			        'codeSystem' => '2.16.840.1.113883.5.1'
		        ]
	        ];

	        // Patient Sex
	        if($patientData['sex'] == 'F'){
		        $recordTarget['patientRole']['patient']['administrativeGenderCode']['@attributes']['displayName'] = 'Female';
	        } elseif($patientData['sex'] == 'M') {
		        $recordTarget['patientRole']['patient']['administrativeGenderCode']['@attributes']['displayName'] = 'Male';
	        }
        }



        if($this->isExcluded('patient_dob')){
	        $recordTarget['patientRole']['patient']['birthTime'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        // Patient Date of Birth
	        $recordTarget['patientRole']['patient']['birthTime'] = [
		        '@attributes' => [
			        'value' => preg_replace('/(\d{4})-(\d{2})-(\d{2}) \d{2}:\d{2}:\d{2}/', '$1$2$3', $patientData['DOB'])
		        ]
	        ];
        }


        if($this->isExcluded('patient_marital_status')){
	        $recordTarget['patientRole']['patient']['maritalStatusCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        if(isset($patientData['marital_status']) && $patientData['marital_status'] != ''){
		        $recordTarget['patientRole']['patient']['maritalStatusCode'] = [
			        '@attributes' => [
				        'code' => $patientData['marital_status'],
				        'codeSystemName' => 'MaritalStatusCode',
				        'displayName' => $this->CombosData->getDisplayValueByListIdAndOptionValue(12, $patientData['marital_status']),
				        'codeSystem' => '2.16.840.1.113883.5.2'
			        ]
		        ];
	        } else {
		        $recordTarget['patientRole']['patient']['maritalStatusCode'] = [
			        '@attributes' => [
				        'nullFlavor' => 'NA',
				        'codeSystemName' => 'MaritalStatusCode',
				        'codeSystem' => '2.16.840.1.113883.5.2'
			        ]
		        ];
	        }
        }


        if($this->isExcluded('patient_race')){
	        $recordTarget['patientRole']['patient']['raceCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        // Patient Race
	        if(isset($patientData['race']) && $patientData['race'] != ''){

		        $race = $this->CombosData->getValuesByListIdAndOptionValue(14, $patientData['race']);

		        if($race !==  false){
			        $recordTarget['patientRole']['patient']['raceCode'] = [
				        '@attributes' => [
					        'code' => $race['code'],
					        'codeSystemName' => $race['code_type'],
					        'displayName' => $race['option_name'],
					        'codeSystem' => $this->codes($race['code_type'])
				        ]
			        ];
		        }else{
			        $recordTarget['patientRole']['patient']['raceCode'] = [
				        '@attributes' => [
					        'nullFlavor' => 'NA'
				        ]
			        ];
		        }
	        } else {
		        $recordTarget['patientRole']['patient']['raceCode'] = [
			        '@attributes' => [
				        'nullFlavor' => 'NA'
			        ]
		        ];
	        }
        }

        if($this->isExcluded('patient_ethnicity')){
	        $recordTarget['patientRole']['patient']['ethnicGroupCode'] = [
		        '@attributes' => [
			        'nullFlavor' => 'NA'
		        ]
	        ];
        }else{
	        // Patient Ethnicity
	        if(isset($patientData['ethnicity']) && $patientData['ethnicity'] != ''){
		        $recordTarget['patientRole']['patient']['ethnicGroupCode'] = [
			        '@attributes' => [
				        'code' => $patientData['ethnicity'] == 'H' ? '2135-2' : '2186-5',
				        'codeSystemName' => 'Race & Ethnicity - CDC',
				        'displayName' => $this->CombosData->getDisplayValueByListIdAndOptionValue(
					        59,
					        $patientData['ethnicity']
				        ),
				        'codeSystem' => '2.16.840.1.113883.6.238'
			        ]
		        ];
	        } else {
		        $recordTarget['patientRole']['patient']['ethnicGroupCode'] = [
			        '@attributes' => [
				        'nullFlavor' => 'NA'
			        ]
		        ];
	        }
        }


        $recordTarget['patientRole']['patient']['birthplace']['place']['addr'] = $this->addressBuilder(
            false,
            false,
            false,
            false,
            false,
            ''
        );

        if($this->isExcluded('patient_preferred_language')){
	        $recordTarget['patientRole']['patient']['languageCommunication'] = [
		        'languageCode' => [
			        '@attributes' => [
				        'nullFlavor' => 'UNK'
			        ]
		        ]
	        ];
        }else{
	        // Patient preferred language communication
	        if(isset($patientData['language']) && $patientData['language'] != ''){
		        $recordTarget['patientRole']['patient']['languageCommunication'] = [
			        'languageCode' => [
				        '@attributes' => [
					        'code' => $patientData['language']
				        ]
			        ],
			        'modeCode' => [
				        '@attributes' => [
					        'code' => 'ESP',
					        'displayName' => 'Expressed spoken',
					        'codeSystem' => '2.16.840.1.113883.5.60',
					        'codeSystemName' => 'LanguageAbilityMode'
				        ]
			        ],
			        'proficiencyLevelCode' => [
				        '@attributes' => [
					        'code' => 'G',
					        'displayName' => 'Good',
					        'codeSystem' => '2.16.840.1.113883.5.61'
				        ]
			        ],
			        'preferenceInd' => [
				        '@attributes' => [
					        'value' => true
				        ]
			        ]
		        ];

	        } else {
		        $recordTarget['patientRole']['patient']['languageCommunication'] = [
			        '@attributes' => [
				        'nullFlavor' => 'UNK'
			        ]
		        ];
	        }
        }



        $org = [];

        $org['id']['@attributes'] = [
            'root' => '2.16.840.1.113883.4.6',
            'assigningAuthorityName' => 'CCD-Author'
        ];
        $org['name']['prefix'] = $this->facility['name'];
        $org['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');
        $org['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );

        $recordTarget['patientRole']['providerOrganization'] = $org;

        unset($Patient, $patientData, $Insurance, $insuranceData);

        return $recordTarget;
    }

    /**
     * Method getAuthor()
     *
     * The author element represents the creator of the clinical document.
     * The author may be a device, or a person. The person is the patient or the patient’s advocate.
     *
     * @return array
     */
    public function getAuthor()
    {
        $author = [
            'time' => [
                '@attributes' => [
                    'value' => $this->timeNow
                ]
            ]
        ];
        $author['assignedAuthor'] = [
            'id' => [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.4.6',
                    'extension' => $this->user['npi'] == '' ? $this->user['id'] : $this->user['npi']
                ]
            ]
        ];
        // Code
        // https://phinvads.cdc.gov/vads/ViewValueSet.action?id=9FD34BBC-617F-DD11-B38D-00188B398520#
        // TODO: Add a taxonomy field on the users form.
        $author['assignedAuthor']['code'] = [
            '@attributes' => [
                'code' => '163WA2000X',
                'displayName' => 'Administrator',
                'codeSystem' => '2.16.840.1.114222.4.11.1066',
                'codeSystemName' => 'Healthcare Provider Taxonomy (NUCC - HIPAA)'
            ]
        ];
        $author['assignedAuthor']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );

        $author['assignedAuthor']['telecom'] = $this->telecomBuilder(
            $this->facility['phone'],
            'WP'
        );

        $author['assignedAuthor']['assignedPerson'] = [
            '@attributes' => [
                'classCode' => 'PSN',
                'determinerCode' => 'INSTANCE'
            ],
            'name' => [
                'given' => $this->user['fname'],
                'family' => $this->user['lname']
            ]
        ];

        $author['assignedAuthor']['representedOrganization'] = [
            'id' => [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.4.6'
                ],
            ],
            'name' => [
                'prefix' => $this->facility['name']
            ]
        ];

        $author['assignedAuthor']
        ['representedOrganization']
        ['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');

        $author['assignedAuthor']['representedOrganization']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );

        return $author;
    }

    /**
     * Method getCustodian()
     *
     * The custodian element represents the organization or person that is in charge of maintaining the document.
     * The custodian is the steward that is entrusted with the care of the document. Every CDA document has
     * exactly one custodian. The custodian participation satisfies the CDA definition of Stewardship.
     * Because CDA is an exchange standard and may not represent the original form of the authenticated document
     * (e.g., CDA could include scanned copy of original), the custodian represents the steward of the
     * original source document. The custodian may be the document originator, a health information exchange,
     * or other responsible party. Also, the custodian may be the patient or an organization acting on behalf
     * of the patient, such as a PHR organization.
     *
     * @return array
     */
    public function getCustodian()
    {
        $custodian = [
            'assignedCustodian' => [
                'representedCustodianOrganization' => [
                    'id' => [
                        '@attributes' => [
                            'root' => '2.16.840.1.113883.4.6'
                        ]
                    ],
                    'name' => [
                        'prefix' => $this->facility['name']
                    ]
                ]
            ]
        ];

        $custodian['assignedCustodian']['representedCustodianOrganization']['telecom'] = $this->telecomBuilder(
            $this->facility['phone'], 'WP'
        );

        $custodian['assignedCustodian']['representedCustodianOrganization']['addr'] = $this->addressBuilder(
            'WP', $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );

        return $custodian;
    }

    /**
     * Method getInformationRecipient()
     *
     * The informationRecipient element records the intended recipient of the information at the time the document
     * is created. For example, in cases where the intended recipient of the document is the patient's
     * health chart, set the receivedOrganization to be the scoping organization for that chart.
     *
     * @return array
     */
    public function getInformationRecipient()
    {

        $recipient = [
            'intendedRecipient' => [
                'informationRecipient' => [
                    'name' => [
                        'given' => $this->user['fname'],
                        'family' => $this->user['lname']
                    ],
                ],
                'receivedOrganization' => [
                    'name' => [
                        'prefix' => $this->facility['name']
                    ]
                ]
            ]
        ];

        return $recipient;
    }

    /**
     * Method getAuthenticator()
     *
     * The combined @root and @extension  attributes to record the authenticator’s identity in a
     * secure, trusted, and unique way.
     *
     * @return array
     */
    public function getAuthenticator()
    {
        $authenticator = [
            'time' => [
                '@attributes' => [
                    'value' => $this->timeNow
                ]
            ],
            'signatureCode' => [
                '@attributes' => [
                    'code' => 'S'
                ],
            ],
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.3.225',
                        'assigningAuthorityName' => $this->facility['name']
                    ]
                ]
            ]
        ];

        $authenticator['assignedEntity']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );

        $authenticator['assignedEntity']['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');
        $authenticator['assignedEntity']['assignedPerson'] = [
            'name' => [
                'given' => $this->user['fname'],
                'family' => $this->user['lname']
            ]
        ];

        return $authenticator;
    }

    /**
     * Method getDocumentationOf()
     * @return array
     */
    public function getDocumentationOf()
    {

	    $encounters = [];

        if($this->eid === 'all_enc'){
            $filters = new stdClass();
            $filters->filter[0] = new stdClass();
            $filters->filter[0]->property = 'pid';
            $filters->filter[0]->value = $this->pid;
            $encounters = $this->Encounter->getEncounters($filters, false, false);
        }elseif (is_numeric($this->eid)){
	        $filters = new stdClass();
	        $filters->filter[0] = new stdClass();
	        $filters->filter[0]->property = 'eid';
	        $filters->filter[0]->value = $this->eid;
	        $encounters = $this->Encounter->getEncounters($filters, false, false);
        }

        // Just do the empty thing for service event.
        if(empty($encounters)){
            $documentationOf = [
                'serviceEvent' => [
                    '@attributes' => [
                        'classCode' => 'PCPR'
                    ],
                    'code' => [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ],
                    'effectiveTime' => [
                        '@attributes' => [
                            'xsi:type' => 'IVL_TS'
                        ],
                        'low' => [
                            '@attributes' => [
                                'nullFlavor' => 'NI'
                            ]
                        ],
                        'high' => [
                            '@attributes' => [
                                'nullFlavor' => 'NI'
                            ]
                        ]
                    ]
                ]
            ];
            return $documentationOf;
        }

        $documentationOf = [
            'serviceEvent' => [
                '@attributes' => [
                    'classCode' => 'PCPR'
                ],
                'code' => [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ],
                'effectiveTime' => [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ],
                    'low' => [
                        '@attributes' => [
                            'value' => $this->parseDate($encounters[0]['service_date'])
                        ]
                    ],
                    'high' => [
                        '@attributes' => [
                            'value' => $this->parseDate($encounters[sizeof($encounters) - 1]['service_date'])
                        ]
                    ]
                ]
            ]
        ];

        // Eliminate duplicates
        $encounters = $this->removeDuplicateKeys('provider_uid',$encounters);

        foreach ($encounters as $encounter) {
            $facility = $this->Facilities->getFacility($encounter['facility']);
            $provider = $this->User->getUserByUid($encounter['provider_uid']);

            $documentationOf['serviceEvent']['performer'][$encounter['eid']] = [
                '@attributes' => [
                    'typeCode' => 'PRF'
                ],
                'templateId' => [
                    '@attributes' => [
                        'root' => '1.3.6.1.4.1.19376.1.5.3.1.2.3'
                    ]
                ],
                'time' => [
                    'low' => [
                        '@attributes' => [
                            'value' => $this->parseDate($encounter['service_date'])
                        ]
                    ],
                    'high' => [
                        '@attributes' => [
                            'value' => $this->parseDate($encounter['service_date'])
                        ]
                    ]
                ],
                'assignedEntity' => [
                    'id' => [
                        '@attributes' => [
                            'root' => UUID::v4()
                        ]
                    ],
                ]
            ];

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['addr'] = $this->addressBuilder(
                'WP',
                $facility['address'] . ' ' . $facility['address_cont'],
                $facility['city'],
                $facility['state'],
                $facility['postal_code'],
                $facility['country_code']
            );

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['telecom'] = $this->telecomBuilder(
                $facility['phone'],
                'WP'
            );

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['assignedPerson'] = [
                'name' => [
                    'prefix' => $provider['title'],
                    'given' => $provider['fname'],
                    'family' => $provider['lname']
                ]
            ];

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization'] = [
                'id' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.4.6'
                    ]
                ],
                'name' => [
                    'prefix' => $facility['name']
                ]
            ];

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization']['telecom'] =
                $this->telecomBuilder($facility['phone'], 'WP');

            $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization']['addr'] =
                $this->addressBuilder(
                    'WP',
                    $facility['address'] . ' ' . $facility['address_cont'],
                    $facility['city'],
                    $facility['state'],
                    $facility['postal_code'],
                    $facility['country_code']
                );

            // Exclude the Provider Information from the documentationOf section
            if($this->isExcluded('provider_information')){
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['assignedPerson']['name'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['addr'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['telecom'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization']['name'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization']['addr'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
                $documentationOf['serviceEvent']['performer'][$encounter['eid']]['assignedEntity']['representedOrganization']['telecom'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
            }
        }


        return $documentationOf;
    }

    /**
     * Method getComponentOf()
     *
     * The componentOf element contains the encompassing encounter for the document. The encompassing encounter
     * represents the setting of the clinical encounter during which the document act(s) or ServiceEvent(s) occurred.
     *
     * In order to represent providers associated with a specific encounter, they are recorded within the
     * encompassingEncounter as participants.
     *
     * In a CCD, the encompassingEncounter may be used when documenting a specific encounter and its participants.
     * All relevant encounters in a CCD may be listed in the encounters section.
     *
     * @return mixed
     */
    public function getComponentOf()
    {

        if ($this->eid == 'no_enc') return;

        $componentOf['encompassingEncounter'] = [
            'id' => [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.4.6'
                ]
            ]
        ];
        $componentOf['encompassingEncounter']['code'] = [
            '@attributes' => [
                'nullFlavor' => 'UNK'
            ]
        ];

	    if($this->isExcluded('visit_date_location')) {
		    $componentOf['encompassingEncounter']['effectiveTime'] = [
			    '@attributes' => [
				    'nullFlavor' => 'UNK'
			    ]
		    ];
	    }else{
		    $componentOf['encompassingEncounter']['effectiveTime'] = [
			    '@attributes' => [
				    'value' => $this->parseDate($this->encounter['service_date'])
			    ]
		    ];
	    }

        $responsibleParty = [
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.4.6'
                    ]
                ],
                'assignedPerson' => [
                    'name' => [
                        'prefix' => $this->encounterProvider['title'],
                        'given' => $this->encounterProvider['fname'],
                        'family' => $this->encounterProvider['lname']
                    ]
                ]
            ]
        ];
        $componentOf['encompassingEncounter']['responsibleParty'] = $responsibleParty;
        unset($responsibleParty);

        $encounterParticipant = [
            '@attributes' => [
                'typeCode' => 'ATND'
            ],
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.4.6'
                    ]
                ],
                'assignedPerson' => [
                    'name' => [
                        'prefix' => $this->encounterProvider['title'],
                        'given' => $this->encounterProvider['fname'],
                        'family' => $this->encounterProvider['lname']
                    ]
                ]
            ]
        ];
        $componentOf['encompassingEncounter']['encounterParticipant'] = $encounterParticipant;
        unset($responsibleParty);

	    if(!$this->isExcluded('visit_date_location')){
		    $location = [
			    'healthCareFacility' => [
				    'id' => [
					    '@attributes' => [
						    'root' => '2.16.840.1.113883.4.6'
					    ]
				    ],
				    'location' => [
					    'name' => [
						    'prefix' => $this->encounterFacility['name']
					    ],
					    'addr' => $this->addressBuilder(
						    'WP',
						    $this->encounterFacility['address'] . ' ' . $this->encounterFacility['address_cont'],
						    $this->encounterFacility['city'],
						    $this->encounterFacility['state'],
						    $this->encounterFacility['postal_code'],
						    $this->encounterFacility['country_code']
					    ),
				    ]
			    ]
		    ];
	    }else{
		    $location = [
			    'healthCareFacility' => [
				    'id' => [
					    '@attributes' => [
						    'root' => '2.16.840.1.113883.4.6'
					    ]
				    ],
				    'location' => [
					    'name' => [
						    '@attributes' => [
							    'nullFlavor' => 'UNK'
						    ]
					    ],
					    'addr' => $this->addressBuilder(
						    false,
						    false,
						    false,
						    false,
						    false,
						    ''
					    ),
				    ]
			    ]
		    ];
	    }
        $componentOf['encompassingEncounter']['location'] = $location;
        unset($location);

        // Exclude the Provider Information from the documentationOf section
        if($this->isExcluded('provider_information')){
            $componentOf['encompassingEncounter']['responsibleParty']['assignedEntity']['assignedPerson']['name'] = [
                '@attributes' => [
                    'nullFlavor' => 'UNK'
                ]
            ];
            $componentOf['encompassingEncounter']['location']['healthCareFacility']['location']['name'] = [
                '@attributes' => [
                    'nullFlavor' => 'UNK'
                ]
            ];
            $componentOf['encompassingEncounter']['location']['healthCareFacility']['location']['addr'] = [
                '@attributes' => [
                    'nullFlavor' => 'UNK'
                ]
            ];
        }

        return $componentOf;

    }

    /**
     * Method getInformant()
     * @return array
     */
    public function getInformant()
    {
        $informant = [];

        $informant['assignedEntity']['id']['@attributes'] = [
            'root' => '2.16.840.1.113883.4.6'
        ];

        $informant['assignedEntity']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );
        $informant['assignedEntity']['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');

        $informant['assignedEntity']['assignedPerson'] = [
            'name' => [
                'given' => $this->user['fname'],
                'family' => $this->user['lname']
            ]
        ];

        return $informant;
    }

    /**
     * Method getInformant()
     *
     * The dataEnterer element represents the person who transferred the content, written or dictated,
     * into the clinical document. To clarify, an author provides the content found within the header or
     * body of a document, subject to their own interpretation; a dataEnterer adds an author's
     * information to the electronic system.
     *
     * @return array
     */
    public function getDataEnterer()
    {
        $dataEnterer['assignedEntity']['id']['@attributes'] = [
            'root' => '2.16.840.1.113883.4.6',
            'extension' => $this->facility['id']
        ];

        $dataEnterer['assignedEntity']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'] . ' ' . $this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );
        $dataEnterer['assignedEntity']['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');

        $dataEnterer['assignedEntity']['assignedPerson'] = [
            'name' => [
                'given' => $this->user['fname'],
                'family' => $this->user['lname']
            ]
        ];

        return $dataEnterer;
    }

    /**
     * Method getPerformerByUid()
     * @param $uid
     * @return array|bool
     */
    public function getPerformerByUid($uid)
    {
        $User = new User();
        $user = $User->getUser($uid);
        unset($User);

        if($user === false) return false;
        $user = (object)$user;

        if($user->facility_id == 0) return false;

        $Facilities = new Facilities();
        $facility = $Facilities->getFacility(['id' => $user->facility_id]);
        if($user === false) return false;
        $facility = (object)$facility;

        $performer = [
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ]
            ]
        ];

        $performer['assignedEntity']['addr'] = $this->addressBuilder(
            'HP',
            $user->street,
            $user->city,
            $user->state,
            $user->postal_code,
            $user->country_code
        );

        $performer['assignedEntity']['telecom'] = $this->telecomBuilder($user->phone);

        $performer['assignedEntity']['representedOrganization'] = [
            'id' => [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.4.6'
                ]
            ]
        ];

        $performer['assignedEntity']['assignedPerson']['name'] = [
            'name' => [
                'prefix' => $this->primaryProvider['title'],
                'given' => $this->primaryProvider['fname'],
                'family' => $this->primaryProvider['lname']
            ]
        ];

        $performer['assignedEntity']['representedOrganization']['name'] = $facility->name;
        $performer['assignedEntity']['representedOrganization']['telecom'] = $this->telecomBuilder($this->facility['phone'], 'WP');
        $performer['assignedEntity']['representedOrganization']['addr'] = $this->addressBuilder(
            'WP',
            $this->facility['address'].' '.$this->facility['address_cont'],
            $this->facility['city'],
            $this->facility['state'],
            $this->facility['postal_code'],
            $this->facility['country_code']
        );


        return $performer;
    }

    public function setReasonOfVisitSection()
    {
        if($this->eid != 'no_enc'){

        	if($this->isExcluded('reason_for_visit')){
		        $reason = [
			        '@attributes' => [
				        'nullFlavor' => 'NA'
			        ],
			        'templateId' => [
				        '@attributes' => [
					        'root' => '2.16.840.1.113883.10.20.22.2.12'
				        ]
			        ],
			        'code' => [
				        '@attributes' => [
					        'code' => '29299-5',
					        'codeSystem' => '2.16.840.1.113883.6.1',
					        'codeSystemName' => 'LOINC',
					        'displayName' => 'Reason for Visit'
				        ]
			        ],
			        'title' => 'Reason for Visit',
			        'text' => ''
		        ];
	        }else{

        		if(!$this->isExcluded('visit_date_location')){

			        $location = ' -- Location: ';
			        if(isset($this->encounterFacility['address']) && $this->encounterFacility['address'] != ''){
				        $location .= $this->encounterFacility['address'] . ' ';
			        }
			        if(isset($this->encounterFacility['address_cont']) && $this->encounterFacility['address_cont'] != ''){
				        $location .= $this->encounterFacility['address_cont'] . ' ';
			        }
			        if(isset($this->encounterFacility['city']) && $this->encounterFacility['city'] != ''){
				        $location .= $this->encounterFacility['city'] . ' ';
			        }
			        if(isset($this->encounterFacility['state']) && $this->encounterFacility['state'] != ''){
				        $location .= $this->encounterFacility['state'] . ' ';
			        }
			        if(isset($this->encounterFacility['postal_code']) && $this->encounterFacility['postal_code'] != ''){
				        $location .= $this->encounterFacility['postal_code'] . ' ';
			        }
			        if(isset($this->encounterFacility['country_code']) && $this->encounterFacility['country_code'] != ''){
				        $location .= $this->encounterFacility['country_code'] . ' ';
			        }

		        }else{
        			$location = '';
		        }


		        $reason = [
			        'templateId' => [
				        '@attributes' => [
					        'root' => '2.16.840.1.113883.10.20.22.2.12'
				        ]
			        ],
			        'code' => [
				        '@attributes' => [
					        'code' => '29299-5',
					        'codeSystem' => '2.16.840.1.113883.6.1',
					        'codeSystemName' => 'LOINC',
					        'displayName' => 'Reason for Visit'
				        ]
			        ],
			        'title' => 'Reason for Visit',
			        'text' => $this->encounter['brief_description'] . $location
		        ];



	        }


            $this->addSection(['section' => $reason]);
        }
    }

    public function setInstructionsSection()
    {

        $Encounter = new Encounter();
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $tempSoap = '';
        if($this->eid === 'all_enc')
        {
            $params->filter[0]->property = 'pid';
            $params->filter[0]->value = $this->pid;
            $allEncounters = $Encounter->getEncounters($params, false, false);
            foreach($allEncounters as $encounter) {
                $soap = $this->Encounter->getSoapByEid($encounter['eid']);
                if(isset($soap['instructions'])) $tempSoap .= $soap['instructions'] . ' ';
            }
        } elseif(is_numeric($this->eid)) {
            $params->filter[0]->property = 'eid';
            $params->filter[0]->value = $this->eid;
            $encounter = $Encounter->getEncounter($params, false, false);
            $soap = $this->Encounter->getSoapByEid($encounter['encounter']['eid']);
            $tempSoap = 'Instructions: '. $soap['instructions'];

            if(!$this->isExcluded('patient_decision_aids')){
	            $codes = [];
	            $DecisionAids = new DecisionAids();
	            $decisionAids = $DecisionAids->getDecisionAidsByTriggerCodes($codes);
	            if(count($decisionAids) > 0){
		            $tempSoap .= ' -- Educational Resources: ' . $decisionAids[0]['instruction_code_description'];
	            }
            }

			unset($decisionAids, $DecisionAids);

        }elseif($this->eid === 'no_enc'){
            $tempSoap = 'No instruction to show';
        }
        $instructions = [
            'templateId' => [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.10.20.22.2.45'
                ]
            ],
            'code' => [
                '@attributes' => [
                    'code' => '69730-0',
                    'codeSystem' => '2.16.840.1.113883.6.1',
                    'codeSystemName' => 'LOINC',
                    'displayName' => 'Instructions'
                ]
            ],
            'title' => 'Instructions',
            'text' => $tempSoap,
            'entry' => [
                '@attributes' => [
                    'nullFlavor' => 'NA'
                ],
                'act' => [
                    '@attributes' => [
                        'classCode' => 'ACT',
                        'moodCode' => 'INT'
                    ],
                    'templateId' => [
                        '@attributes' => [
                            'root' => '2.16.840.1.113883.10.20.22.4.20'
                        ]
                    ],
                    'code' => [
                        '@attributes' => [
                            'nullFlavor' => 'NA'
                        ]
                    ],
                    'statusCode' => [
                        '@attributes' => [
                            'nullFlavor' => 'NA'
                        ]
                    ]
                ]
            ]
        ];

        if($this->isExcluded('clinical_instructions')){
            $instructions['text'] = 'No instruction for this patient.';
        }

        $this->addSection(['section' => $instructions]);
    }

    public function setReasonForReferralSection()
    {

        if(isset($this->encounter)) {

            $Referrals = new Referrals();
            $ReferringProviders = new ReferringProviders();

            $referral = $Referrals->getPatientReferralByEid($this->encounter['eid']);

            if($referral == false){
				return;
            }

            $referringProvider = $ReferringProviders->getReferringProviderById($referral['refer_to']);

            unset($Referrals, $ReferringProviders);

            $text = '';

            if($referral['referal_reason'] != ''){
	            $text .= $referral['referal_reason'] . ', ';
            }

	        $text .= $referringProvider['title'] . ' ' . $referringProvider['fname'] . ' ' . $referringProvider['lname'] . ' ';

            if(is_array($referringProvider['facilities']) && isset($referringProvider['facilities'][0])){

            	if($referringProvider['facilities'][0]['phone_number'] != ''){
		            $text .= 'Tel: ' . $referringProvider['facilities'][0]['phone_number'] . ' ';
	            }
	            if($referringProvider['facilities'][0]['name'] != '') {
		            $text .= 'Name: ' . $referringProvider['facilities'][0]['name'] . ' ';
	            }
	            if($referringProvider['facilities'][0]['address'] != '') {
		            $text .= 'Address: ' . $referringProvider['facilities'][0]['address'] . ' ';
	            }
	            if($referringProvider['facilities'][0]['address_cont'] != '') {
		            $text .= $referringProvider['facilities'][0]['address_cont'] . ', ';
	            }
	            if($referringProvider['facilities'][0]['city'] != '') {
		            $text .= $referringProvider['facilities'][0]['city'] . ' ';
	            }
	            if($referringProvider['facilities'][0]['state'] != '') {
		            $text .= $referringProvider['facilities'][0]['state'] . ' ';
	            }
	            if($referringProvider['facilities'][0]['postal_code'] != '') {
		            $text .= $referringProvider['facilities'][0]['postal_code'];
	            }
            }

            if(isset($referral['referral_date'])){
	            $text .= ' -- Schedule: ' . date('F j, Y h:i A', strtotime($referral['referral_date']));
            }

            $reasonForReferral = [
                'templateId' => [
                    '@attributes' => [
                        'root' => '1.3.6.1.4.1.19376.1.5.3.1.3.1'
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => '42349-1',
                        'codeSystem' => '2.16.840.1.113883.6.1',
                        'codeSystemName' => 'LOINC',
                        'displayName' => 'Reason for Referral'
                    ]
                ],
                'title' => 'Reason for Referral',
                'text' => $text
            ];

            $this->addSection(['section' => $reasonForReferral]);
        }
    }

    /**
     * Method setProceduresSection()
     */
    public function setProceduresSection() {

        $Procedures = new Procedures();
        $proceduresData = $Procedures->getPatientProceduresByPid($this->pid);
        unset($Procedures);

        $procedures = [];

        if(empty($proceduresData) || $this->isExcluded('procedures')){
            $procedures['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $procedures['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredProcedures ? '2.16.840.1.113883.10.20.22.2.7.1' : '2.16.840.1.113883.10.20.22.2.7'
            ]
        ];
        $procedures['code'] = [
            '@attributes' => [
                'code' => '47519-4',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $procedures['title'] = 'Procedures';
        $procedures['text'] = '';

        if($this->isExcluded('procedures')) {
            $this->addSection(['section' => $procedures]);
            return;
        };

        if(!empty($proceduresData)){

            $procedures['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'Procedure'
                                    ],
                                    [
                                        '@value' => 'Date'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];
            $procedures['entry'] = [];

            foreach($proceduresData as $item){
                $procedures['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => $this->clean($item['code_text'])
                        ],
                        [
                            '@value' => $this->parseDateToText($item['create_date'])
                        ]
                    ]

                ];

                //  Procedure Activity Procedure
                $entry = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'procedure' => [
                        '@attributes' => [
                            'classCode' => 'PROC',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.14'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => $item['code'],
                                'codeSystem' => $this->codes($item['code_type']),
                                'displayName' => $this->clean($item['code_text'])
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ],
                        'effectiveTime' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['create_date'])
                            ]
                        ]
                    ]
                ];

                if($item['uid'] > 0){
                    $entry['procedure']['performer'] = $this->getPerformerByUid($item['uid']);
                };

                $entry['procedure']['methodCode'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];

                $procedures['entry'][] = $entry;
            }
        }

        if($this->requiredProcedures || isset($procedures['entry'])){
            $this->addSection(['section' => $procedures]);
        }
        unset($proceduresData, $procedures);
    }

    /**
     * Method setVitalsSection()
     *
     * The Vital Signs section contains relevant vital signs for the context and use case of the document type,
     * such as blood pressure, heart rate, respiratory rate, height, weight, body mass index, head circumference,
     * pulse oximetry, temperature and body surface area. The section should include notable vital signs such
     * as the most recent, maximum and/or minimum, baseline, or relevant trends.
     *
     * Vital signs are represented in the same way as other results, but are aggregated into their own section
     * to follow clinical conventions.
     */
    public function setVitalsSection() {
        $Vitals = new Vitals();
        $vitalsData = $Vitals->getVitalsByPid($this->pid);

        if(empty($vitalsData) || $this->isExcluded('vitals')){
            $vitals['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $vitals['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredVitals ? '2.16.840.1.113883.10.20.22.2.4.1' : '2.16.840.1.113883.10.20.22.2.4'
            ]
        ];
        $vitals['code'] = [
            '@attributes' => [
                'code' => '8716-3',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $vitals['title'] = 'Vital Signs';
        $vitals['text'] = '';


        if($this->isExcluded('vitals')) {
            $this->addSection(['section' => $vitals]);
            return;
        };

        if(!empty($vitalsData)){

            $vitals['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@attributes' => [
                                            'align' => 'right'
                                        ],
                                        '@value' => 'Date / Time:'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@attributes' => [
                                            'align' => 'left'
                                        ],
                                        '@value' => 'Height'
                                    ]
                                ]

                            ],
                            [
                                'th' => [
                                    [
                                        '@attributes' => [
                                            'align' => 'left'
                                        ],
                                        '@value' => 'Weight'
                                    ]
                                ]

                            ],
                            [
                                'th' => [
                                    [
                                        '@attributes' => [
                                            'align' => 'left'
                                        ],
                                        '@value' => 'Blood Pressure'
                                    ]
                                ]
                            ],
                            [
                                'th' => [
                                    [
                                        '@attributes' => [
                                            'align' => 'left'
                                        ],
                                        '@value' => 'BMI (Body Mass Index)'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $vitals['entry'] = [];

            foreach($vitalsData as $item){
                // strip date (yyyy-mm-dd hh:mm:ss => yyyymmdd)
                $date = $this->parseDate($item['date']);
                // Date
                $vitals['text']['table']['thead']['tr'][0]['th'][] = [
                    '@value' => date('F j, Y h:i A', strtotime($item['date']))
                ];

	            // Height
	            $height = $this->height_measure == 'in' ? $item['height_in'] : $item['height_cm'];
	            $vitals['text']['table']['tbody']['tr'][0]['td'][] = [
		            '@value' => $height . ' '.$this->height_measure
	            ];
                // Weight
	            $weight = $this->weight_measure == 'lbs' ? $item['weight_lbs'] : $item['weight_kg'];
                $vitals['text']['table']['tbody']['tr'][1]['td'][] = [
                    '@value' => $weight . ' ' . $this->weight_measure
                ];

                // Blood Pressure
                $vitals['text']['table']['tbody']['tr'][2]['td'][] = [
                    '@value' => $item['bp_systolic'] . '/' . $item['bp_diastolic'] . ' mmHg'
                ];

                // BMI (Body Mass Index)
                $vitals['text']['table']['tbody']['tr'][3]['td'][] = [
                    '@value' => $item['bmi'] . ' kg/m2'
                ];

                // Code Entry
                $entry = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'organizer' => [
                        '@attributes' => [
                            'classCode' => 'CLUSTER',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.26'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '46680005',
                                'codeSystemName' => 'SNOMED CT',
                                'codeSystem' => '2.16.840.1.113883.6.96',
                                'displayName' => 'Vital signs'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ],
                        'effectiveTime' => [
                            '@attributes' => [
                                'value' => $date
                            ]
                        ],
                        'component' => [
                            [
                                'observation' => [
                                    '@attributes' => [
                                        'classCode' => 'OBS',
                                        'moodCode' => 'EVN'
                                    ],
                                    'templateId' => [
                                        '@attributes' => [
                                            'root' => '2.16.840.1.113883.10.20.22.4.27'
                                        ]
                                    ],
                                    'id' => [
                                        '@attributes' => [
                                            'root' => UUID::v4()
                                        ]
                                    ],
                                    'code' => [
                                        '@attributes' => [
                                            'code' => '8302-2',
                                            'codeSystemName' => 'LOINC',
                                            'codeSystem' => '2.16.840.1.113883.6.1',
                                            'displayName' => 'Height'
                                        ]
                                    ],
                                    'statusCode' => [
                                        '@attributes' => [
                                            'code' => 'completed'
                                        ]
                                    ],
                                    'effectiveTime' => [
                                        '@attributes' => [
                                            'value' => $date
                                        ]
                                    ],
                                    'value' => [
                                        '@attributes' => [
                                            'xsi:type' => 'PQ',
                                            'value' => $height,
                                            'unit' => $this->height_measure
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'observation' => [
                                    '@attributes' => [
                                        'classCode' => 'OBS',
                                        'moodCode' => 'EVN'
                                    ],
                                    'templateId' => [
                                        '@attributes' => [
                                            'root' => '2.16.840.1.113883.10.20.22.4.2'
                                        ]
                                    ],
                                    'id' => [
                                        '@attributes' => [
                                            'root' => UUID::v4()
                                        ]
                                    ],
                                    'code' => [
                                        '@attributes' => [
                                            'code' => '3141-9',
                                            'codeSystemName' => 'LOINC',
                                            'codeSystem' => '2.16.840.1.113883.6.1',
                                            'displayName' => 'Weight Measured'
                                        ]
                                    ],
                                    'statusCode' => [
                                        '@attributes' => [
                                            'code' => 'completed'
                                        ]
                                    ],
                                    'effectiveTime' => [
                                        '@attributes' => [
                                            'value' => $date
                                        ]
                                    ],
                                    'value' => [
                                        '@attributes' => [
                                            'xsi:type' => 'PQ',
                                            'value' => $weight,
                                            'unit' => $this->weight_measure
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'observation' => [
                                    '@attributes' => [
                                        'classCode' => 'OBS',
                                        'moodCode' => 'EVN'
                                    ],
                                    'templateId' => [
                                        '@attributes' => [
                                            'root' => '2.16.840.1.113883.10.20.22.4.2'
                                        ]
                                    ],
                                    'id' => [
                                        '@attributes' => [
                                            'root' => UUID::v4()
                                        ]
                                    ],
                                    'code' => [
                                        '@attributes' => [
                                            'code' => '8480-6',
                                            'codeSystemName' => 'LOINC',
                                            'codeSystem' => '2.16.840.1.113883.6.1',
                                            'displayName' => 'BP Systolic'
                                        ]
                                    ],
                                    'statusCode' => [
                                        '@attributes' => [
                                            'code' => 'completed'
                                        ]
                                    ],
                                    'effectiveTime' => [
                                        '@attributes' => [
                                            'value' => $date
                                        ]
                                    ],
                                    'value' => [
                                        '@attributes' => [
                                            'xsi:type' => 'PQ',
                                            'value' => $item['bp_systolic'],
                                            'unit' => 'mm[Hg]'
                                        ]
                                    ]
                                ]

                            ],
                            [
                                'observation' => [
                                    '@attributes' => [
                                        'classCode' => 'OBS',
                                        'moodCode' => 'EVN'
                                    ],
                                    'templateId' => [
                                        '@attributes' => [
                                            'root' => '2.16.840.1.113883.10.20.22.4.2'
                                        ]
                                    ],
                                    'id' => [
                                        '@attributes' => [
                                            'root' => UUID::v4()
                                        ]
                                    ],
                                    'code' => [
                                        '@attributes' => [
                                            'code' => '8462-4',
                                            'codeSystemName' => 'LOINC',
                                            'codeSystem' => '2.16.840.1.113883.6.1',
                                            'displayName' => 'BP Diastolic'
                                        ]
                                    ],
                                    'statusCode' => [
                                        '@attributes' => [
                                            'code' => 'completed'
                                        ]
                                    ],
                                    'effectiveTime' => [
                                        '@attributes' => [
                                            'value' => $date
                                        ]
                                    ],
                                    'value' => [
                                        '@attributes' => [
                                            'xsi:type' => 'PQ',
                                            'value' => $item['bp_diastolic'],
                                            'unit' => 'mm[Hg]'
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'observation' => [
                                    '@attributes' => [
                                        'classCode' => 'OBS',
                                        'moodCode' => 'EVN'
                                    ],
                                    'templateId' => [
                                        '@attributes' => [
                                            'root' => '2.16.840.1.113883.10.20.22.4.2'
                                        ]
                                    ],
                                    'id' => [
                                        '@attributes' => [
                                            'root' => UUID::v4()
                                        ]
                                    ],
                                    'code' => [
                                        '@attributes' => [
                                            'code' => '39156-5',
                                            'codeSystemName' => 'LOINC',
                                            'codeSystem' => '2.16.840.1.113883.6.1',
                                            'displayName' => 'Body mass index (BMI) [Ratio]'
                                        ]
                                    ],
                                    'statusCode' => [
                                        '@attributes' => [
                                            'code' => 'completed'
                                        ]
                                    ],
                                    'effectiveTime' => [
                                        '@attributes' => [
                                            'value' => $date
                                        ]
                                    ],
                                    'value' => [
                                        '@attributes' => [
                                            'xsi:type' => 'PQ',
                                            'value' => $item['bmi'],
                                            'unit' => 'kg/m2'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                $vitals['entry'][] = $entry;
            }
        }

        if($this->requiredVitals || isset($vitals['entry'])){
            $this->addSection(['section' => $vitals]);
        }
        unset($vitalsData, $vitals);

    }

    /**
     * Method setImmunizationsSection()
     */
    public function setImmunizationsSection() {

        $Immunizations = new Immunizations();
        $immunizationsData = $Immunizations->getPatientImmunizationsByPid($this->pid);

        unset($Immunizations);

        if(empty($immunizationsData) || $this->isExcluded('immunizations')){
            $immunizations['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $immunizations['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredImmunization ? '2.16.840.1.113883.10.20.22.2.2.1' : '2.16.840.1.113883.10.20.22.2.2'
            ]
        ];
        $immunizations['code'] = [
            '@attributes' => [
                'code' => '11369-6',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $immunizations['title'] = 'Immunizations';
        $immunizations['text'] = '';

        if($this->isExcluded('immunizations')) {
            $this->addSection(['section' => $immunizations]);
            return;
        };

        if(!empty($immunizationsData)){

            $immunizations['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'Vaccine'
                                    ],
                                    [
                                        '@value' => 'Date'
                                    ],
                                    [
                                        '@value' => 'Status'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];
            $immunizations['entry'] = [];

            foreach($immunizationsData as $item){

                $administered_by = $this->User->getUserByUid($item['administered_uid']);

                $immunizations['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => ucwords($item['vaccine_name'])
                        ],
                        [
                            '@value' => date('F j Y', strtotime($item['administered_date']))
                        ],
                        [
                            '@value' => 'Completed'
                        ]
                    ]
                ];

                $entry['substanceAdministration'] = [
                    '@attributes' => [
                        'classCode' => 'SBADM',
                        'moodCode' => 'EVN',
                        'negationInd' => 'false',
                        'nullFlavor' => 'NI'
                    ],
                    'templateId' => [
                        '@attributes' => [
                            'root' => '2.16.840.1.113883.10.20.22.4.52'
                        ]
                    ],
                    'id' => [
                        '@attributes' => [
                            'root' => UUID::v4()
                        ]
                    ],
                    'code' => [
                        '@attributes' => [
                            'xsi:type' => 'CE',
                            'code' => 'IMMUNIZ',
                            'codeSystem' => '2.16.840.1.113883.5.4',
                            'codeSystemName' => 'ActCode'
                        ]
                    ],
                    'statusCode' => [
                        '@attributes' => [
                            'code' => 'completed'
                        ]
                    ],
                    'effectiveTime' => [
                        '@attributes' => [
                            'value' => $this->parseDate($item['administered_date'])
                        ]
                    ]
                ];

                if(isset($item['administer_amount']) && $item['administer_amount'] != ''){
                    $entry['substanceAdministration']['doseQuantity'] = [
                        '@attributes' => [
                            'value' => $item['administer_amount'],
                            'unit' => $item['administer_units']
                        ]
                    ];
                }else{
                    $entry['substanceAdministration']['doseQuantity'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                $entry['substanceAdministration']['consumable'] = [
                    'manufacturedProduct' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.54'
                            ]
                        ],
                        'manufacturedMaterial' => [
                            'code' => [
                                '@attributes' => [
                                    'code' => $item['code'],
                                    'codeSystemName' => 'CVX',
                                    'codeSystem' => '2.16.840.1.113883.12.292',
                                    'displayName' => ucwords($item['vaccine_name'])
                                ]
                            ]
                        ]
                    ]
                ];

                if(isset($item['lot_number']) && $item['lot_number'] != ''){
                    $entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['lotNumberText'] = $item['lot_number'];
                } else {
                    $entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturedMaterial']['lotNumberText'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                if(isset($item['manufacturer']) && $item['manufacturer'] != ''){
                    $entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturerOrganization'] = [
                        'name' => $item['manufacturer']

                    ];
                } else {
                    $entry['substanceAdministration']['consumable']['manufacturedProduct']['manufacturerOrganization'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                // administered by...
                $entry['substanceAdministration']['performer']['assignedEntity'] = [
                    'id' => [
                        '@attributes' => [
                            'root' => 'NA'
                        ]
                    ]
                ];
                if($administered_by !== false){
                    $entry['substanceAdministration']['performer']['assignedEntity']['code'] = [
                        '@attributes' => [
                            'code' => $administered_by['taxonomy'],
                            'codeSystem' => '2.16.840.1.114222.4.11.1066',
                            'codeSystemName' => 'NUCC Health Care Provider Taxonomy',
                            'displayName' => $administered_by['title'] . ' ' .
                                $administered_by['fname'] . ' ' .
                                $administered_by['mname'] . ' ' .
                                $administered_by['lname']
                        ]
                    ];
                } else {
                    $entry['substanceAdministration']['performer']['assignedEntity']['code'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                // immunization education
                if(isset($item['education_date']) && $item['education_date'] != '0000-00-00'){

                    $entry['substanceAdministration']['entryRelationship'] = [
                        '@attributes' => [
                            'typeCode' => 'SUBJ',
                            'inversionInd' => 'true'
                        ],
                        'act' => [
                            '@attributes' => [
                                'classCode' => 'ACT',
                                'moodCode' => 'INT'
                            ],
                            'templateId' => [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.20'
                                ]
                            ],
                            'code' => [
                                '@attributes' => [
                                    'code' => '171044003',
                                    'codeSystem' => '2.16.840.1.113883.6.96',
                                    'displayName' => 'immunization education'
                                ]
                            ],
                            'statusCode' => [
                                '@attributes' => [
                                    'code' => 'completed'
                                ]
                            ]
                        ]
                    ];
                }

                $immunizations['entry'][] = $entry;
            }

        }

        if($this->requiredImmunization || isset($immunizations['entry'])){
            $this->addSection(['section' => $immunizations]);
        }
        unset($immunizationsData, $immunizations);
    }

    /**
     * Method setMedicationsSection()
     *
     * The Medications Section contains a patient's current medications and pertinent medication history.
     * At a minimum, the currently active medications are listed. An entire medication history is an option.
     * The section can describe a patient's prescription and dispense history and information about
     * intended drug monitoring.
     */
    public function setMedicationsSection() {

        $Medications = new Medications();
        $medicationsData = $Medications->getPatientActiveMedicationsByPid($this->pid, false);
        unset($Medications);

        if(empty($medicationsData) || $this->isExcluded('medications')){
            $medications['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $medications['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredMedications ? '2.16.840.1.113883.10.20.22.2.1.1' : '2.16.840.1.113883.10.20.22.2.1'
            ]
        ];
        $medications['code'] = [
            '@attributes' => [
                'code' => '10160-0',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $medications['title'] = 'Medications';
        $medications['text'] = '';

        if($this->isExcluded('medications')) {
            $this->addSection(['section' => $medications]);
            return;
        };

        if(!empty($medicationsData)){

            $medications['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'RxNorm'
                                    ],
                                    [
                                        '@value' => 'Medication'
                                    ],
                                    [
                                        '@value' => 'Instructions'
                                    ],
                                    [
                                        '@value' => 'Start Date'
                                    ],
                                    [
                                        '@value' => 'Status'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];

            $medications['entry'] = [];

            foreach($medicationsData as $item){

            	$active = $this->isActiveByDate($item['end_date']);

                $medications['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => $item['RXCUI']
                        ],
                        [
                            '@value' => $item['STR'] . ' ' . $item['dose'] . ' ' . $item['form']
                        ],
                        [
                            '@value' => isset($item['directions']) ? $item['directions'] : ' '
                        ],
                        [
                            '@value' => $item['begin_date'] ? date('F j, Y', strtotime($item['begin_date'])) : ' '
                        ],
                        [
                            '@value' => $active ? 'Active' : 'Inactive'
                        ]
                    ]
                ];

                $entry['substanceAdministration']['@attributes'] = [
                    'classCode' => 'SBADM',
                    'moodCode' => 'EVN'
                ];

                $entry['substanceAdministration']['templateId'] = [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.10.20.22.4.16'
                    ]
                ];

                $entry['substanceAdministration']['id'] = [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ];

                $entry['substanceAdministration']['text'] = $item['STR'];

                if($active) {
                    $entry['substanceAdministration']['statusCode'] = [
                        '@attributes' => [
                            'code' => 'active'
                        ]
                    ];
                } else {
                    $entry['substanceAdministration']['statusCode'] = [
                        '@attributes' => [
                            'code' => 'inactive'
                        ]
                    ];
                }

                $entry['substanceAdministration']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];

                $entry['substanceAdministration']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'value' => $this->parseDate($item['begin_date'])
                    ]
                ];

                if(!isset($item['end_date']) || $item['end_date'] == '0000-00-00' || $item['end_date'] == '0000-00-00 00:00:00'){
	                $entry['substanceAdministration']['effectiveTime']['high'] = [
		                '@attributes' => [
			                'nullFlavor' => 'NI'
		                ]
	                ];
                } else {
	                $entry['substanceAdministration']['effectiveTime']['high'] = [
		                '@attributes' => [
			                'value' => $this->parseDate($item['end_date'])
		                ]
	                ];
                }

                $entry['substanceAdministration']['consumable'] = [
                    'manufacturedProduct' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.23'
                            ]
                        ],
                        'manufacturedMaterial' => [
                            'code' => [
                                '@attributes' => [
                                    'code' => $item['RXCUI'],
                                    'codeSystem' => '2.16.840.1.113883.6.88',
                                    'displayName' => ucwords($item['STR']),
                                    'codeSystemName' => 'RxNorm'
                                ]
                            ]
                        ]
                    ]
                ];

                $performer = [
                    'assignedEntity' => [
                        'id' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.4.6'
                            ]
                        ]
                    ]
                ];

                $performer['assignedEntity']['addr'] = $this->addressBuilder(
                    'WP',
                    $this->encounterFacility['address'] . ' ' . $this->encounterFacility['address_cont'],
                    $this->encounterFacility['city'],
                    $this->encounterFacility['state'],
                    $this->encounterFacility['postal_code'],
                    $this->encounterFacility['country_code']
                );

                $performer['assignedEntity']['telecom'] = $this->telecomBuilder($this->encounterFacility['phone'], 'WP');

                $performer['assignedEntity']['representedOrganization'] = [
                    'name' => $this->encounterFacility['name']
                ];

                $performer['assignedEntity']['representedOrganization']['telecom'] = $this->telecomBuilder($this->encounterFacility['phone'], 'WP');
                $performer['assignedEntity']['representedOrganization']['addr'] = $this->addressBuilder(
                    'WP',
                    $this->encounterFacility['address'] . ' ' . $this->encounterFacility['address_cont'],
                    $this->encounterFacility['city'],
                    $this->encounterFacility['state'],
                    $this->encounterFacility['postal_code'],
                    $this->encounterFacility['country_code']
                );

                $entry['substanceAdministration']['performer'] = $performer;
                unset($performer);

                $entry['substanceAdministration']['participant'] = [
                    '@attributes' => [
                        'typeCode' => 'CSM'
                    ],
                    'participantRole' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.24'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '412307009',
                                'codeSystem' => '2.16.840.1.113883.6.96',
                                'codeSystemName' => 'SNOMED',
                                'displayName' => 'drug vehicle'
                            ]
                        ],
                        'playingEntity' => [
                            '@attributes' => [
                                'classCode' => 'MMAT'
                            ],
                            'code' => [
                                '@attributes' => [
                                    'nullFlavor' => 'UNK'
                                ]
                            ],
                            'name' => [
                                '@attributes' => [
                                    'nullFlavor' => 'UNK'
                                ]
                            ]
                        ]
                    ]
                ];

                $medications['entry'][] = $entry;
                unset($entry);
            }

        }

        if($this->requiredMedications || isset($medications['entry'])){
            $this->addSection(['section' => $medications]);
        }
        unset($medicationsData, $medications);
    }

    /**
     * Method setMedicationsAdministeredSection()
     *
     * The Medications Administered Section contains medications and fluids administered during a procedure.
     * The section may also contain the procedure's encounter or other activity, excluding anesthetic medications.
     * This section is not intended for ongoing medications and medication history.
     */
    public function setMedicationsAdministeredSection() {


	    $Medications = new Medications();
	    $medicationsData = $Medications->getPatientAdministeredMedicationsByPidAndEid($this->pid, $this->eid);

	    if($this->eid == 'no_enc' || $this->isExcluded('administered')){
		    $medications['@attributes'] = [
			    'nullFlavor' => 'NI'
		    ];
	    }elseif(empty($medicationsData)){
		    $medications['@attributes'] = [
			    'nullFlavor' => 'NI'
		    ];
	    }

        $medications['templateId'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.2.38'
            ]
        ];
        $medications['code'] = [
            '@attributes' => [
                'code' => '29549-3',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1',
                'displayName' => 'Administered Medications'
            ]
        ];
        $medications['title'] = 'Medications Administered';
        $medications['text'] = '';

        unset($Medications);

        if($this->eid == 'no_enc' || $this->isExcluded('administered') || empty($medicationsData)){
	        $this->addSection(['section' => $medications]);
	        return;
        }

        if(!empty($medicationsData)){
            $medications['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'RxNorm'
                                    ],
                                    [
                                        '@value' => 'Medication'
                                    ],
                                    [
                                        '@value' => 'Instructions'
                                    ],
                                    [
                                        '@value' => 'Date'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];


            // --- 3.51 Medication Activity (V2)
            $medications['entry'] = [];

            foreach($medicationsData as $item){
                $medications['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($item['RXCUI']) ? $item['RXCUI'] : ''
                        ],
                        [
                            '@value' => isset($item['STR']) ? $item['STR'] : ''
                        ],
                        [
                            '@value' => isset($item['directions']) ? $item['directions'] : ''
                        ],
                        [
                            '@value' => date('F j, Y', strtotime($item['administered_date']))
                        ]
                    ]

                ];

                $entry['substanceAdministration']['@attributes'] = [
                    'classCode' => 'SBADM',
                    'moodCode' => 'EVN'
                ];

                $entry['substanceAdministration']['templateId'] = [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.10.20.22.4.16'
                    ]
                ];

                $entry['substanceAdministration']['id'] = [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ];

                $entry['substanceAdministration']['text'] = $item['directions'];

                $entry['substanceAdministration']['statusCode'] = [
                    '@attributes' => [
                        'code' => 'Active'
                    ]
                ];

                $entry['substanceAdministration']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];

                $entry['substanceAdministration']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];

                $entry['substanceAdministration']['effectiveTime']['high'] = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];

                $entry['substanceAdministration']['consumable'] = [
                    'manufacturedProduct' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.23'
                            ]
                        ],
                        'manufacturedMaterial' => [
                            'code' => [
                                '@attributes' => [
                                    'code' => $item['RXCUI'],
                                    'codeSystem' => '2.16.840.1.113883.6.88',
                                    'displayName' => ucwords($item['STR']),
                                    'codeSystemName' => 'RxNorm'
                                ]
                            ]
                        ]
                    ]
                ];

                $performer = [
                    'assignedEntity' => [
                        'id' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.4.6'
                            ]
                        ]
                    ]
                ];

                $performer['assignedEntity']['addr'] = $this->addressBuilder(
                    'WP',
                    $this->encounterFacility['address'] . ' ' . $this->encounterFacility['address_cont'],
                    $this->encounterFacility['city'],
                    $this->encounterFacility['state'],
                    $this->encounterFacility['postal_code'],
                    $this->encounterFacility['country_code']
                );

                $performer['assignedEntity']['telecom'] = $this->telecomBuilder($this->encounterFacility['phone'], 'WP');

                $performer['assignedEntity']['representedOrganization'] = [
                    'name' => $this->encounterFacility['name']
                ];

                $performer['assignedEntity']['representedOrganization']['telecom'] = $this->telecomBuilder($this->encounterFacility['phone'], 'WP');
                $performer['assignedEntity']['representedOrganization']['addr'] = $this->addressBuilder(
                    'WP',
                    $this->encounterFacility['address'] . ' ' . $this->encounterFacility['address_cont'],
                    $this->encounterFacility['city'],
                    $this->encounterFacility['state'],
                    $this->encounterFacility['postal_code'],
                    $this->encounterFacility['country_code']
                );

                $entry['substanceAdministration']['performer'] = $performer;
                unset($performer);

                $entry['substanceAdministration']['participant'] = [
                    '@attributes' => [
                        'typeCode' => 'CSM'
                    ],
                    'participantRole' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.24'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '412307009',
                                'codeSystem' => '2.16.840.1.113883.6.96',
                                'codeSystemName' => 'SNOMED',
                                'displayName' => 'drug vehicle'
                            ]
                        ],
                        'playingEntity' => [
                            '@attributes' => [
                                'classCode' => 'MMAT'
                            ],
                            'code' => [
                                '@attributes' => [
                                    'nullFlavor' => 'UNK'
                                ]
                            ],
                            'name' => [
                                '@attributes' => [
                                    'nullFlavor' => 'UNK'
                                ]
                            ]
                        ]
                    ]
                ];

                $medications['entry'][] = $entry;
                unset($entry);
            }

        }

        if($this->requiredMedications || isset($medications['entry'])){
            $this->addSection(['section' => $medications]);
        }
        unset($medicationsData, $medications);
    }

    /**
     * CARE PLAN FRAMEWORK
     *
     * A Care Plan is a consensus-driven dynamic plan that represents all of a patient’s and Care Team Members’
     * A Care Plan is a consensus-driven dynamic plan that represents all of a patient’s and Care Team Members’
     * prioritized concerns, goals, and planned interventions. It serves as a blueprint shared by all
     * Care Team Members, including the patient, to guide the Care Team Members (including Patients,
     * their caregivers, providers and patient’s care. A Care Plan integrates multiple interventions proposed by
     * multiple providers and disciplines for multiple conditions.
     *
     * A Care Plan represents one or more Plan(s) of Care and serves to reconcile and resolve conflicts between
     * the various Plans of Care developed for a specific patient by different providers. While both a plan of
     * care and a care plan include the patient’s life goals and require Care Team Members (including patients)
     * to prioritize goals and interventions, the reconciliation process becomes more complex as the number of
     * plans of care increases. The Care Plan also serves to enable longitudinal coordination of care.
     *
     * The CDA Care Plan represents an instance of this dynamic Care Plan at a point in time.
     * The CDA document itself is NOT dynamic.
     *
     * Key differentiators between a Care Plan CDA and CCD (another “snapshot in time” document):
     * •  Requires relationships between various acts:
     * o  Health Concerns
     * o  Problems
     * o  Interventions
     * o  Goals
     * o  Outcomes
     * •  Provides the ability to identify patient and provider priorities with each act
     * •  Provides a header participant to indicate occurrences of Care Plan review
     *
     */
    public function setCareOfPlanSection(){

        if(!$this->requiredCareOfPlan) return;

        // 1.1 - Care Plan (NEW)
        $careOfPlan['template'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.1.15'
            ]
        ];
        $careOfPlan['id'] = [
            '@attributes' => [
                'root' => UUID::v4()
            ]
        ];
        $planOfCare['code'] = [
            '@attributes' => [
                'code' => 'CarePlan-X',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];

        // 1.1.1 - authenticator
        // [0..1] Zero or one
        // This authenticator represents patient agreement or sign-off of the Care Plan
        $careOfPlan['authenticator'] = [
            'time' => [
                '@attributes' => [
                    'value' => '' // Date of the patient sign-off
                ]
            ],
            'signatureCode' => [
                '@attributes' => [
                    'code' => 'S'
                ]
            ],
            'signatureText' => [
                '@attributes' => [
                    'mediaType' => 'text/xml',
                    'representation' => 'B64'
                ],
                base64_encode('')
            ],
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'extension' => '996-756-495',
                        'root' => '2.16.840.1.113883.19.5'
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => 'ONESELF',
                        'displayName' => 'Oneself',
                        'codeSystem' => '2.16.840.1.113883.5.111',
                        'codeSystemName' => 'HL7 Role code'
                    ]
                ]
            ]
        ];

        // 1.1.2 - participant - Patient Itself
        // [0..*] Zero or more
        // This participant represents the Care Plan Review. If the date in the time element is in the past,
        // then this review has already taken place. If the date in the time element is in the future,
        // then this is the date of the next scheduled review.
        $careOfPlan['participant'] = [
            '@attributes' => [
                'typeCode' => 'IND'
            ],
            'functionCode' => [
                '@attributes' => [
                    'code' => '425268008',
                    'codeSystem' => '2.16.840.1.113883.6.96',
                    'codeSystemName' => 'SNOMED CT',
                    'displayName' => 'Review of Care Plan'
                ]
            ],
            'time' => [
                '@attributes' => [
                    'value' => '' // Check the participant description for more info.
                ]
            ],
            // Code	Code System	Print Name
            // ONESELF  RoleCode    self
            // MTH      RoleCode	mother
            // FTH      RoleCode	father
            // DAU      RoleCode	natural daughter
            // SON      RoleCode	natural son
            // DAUINLAW	RoleCode	daughter in-law
            // SONINLAW	RoleCode	son in-law
            // GUARD	RoleCode	guardian
            // HPOWATT	RoleCode	healthcare power of attorney
            'associatedEntity' => [
                '@attributes' => [
                    'classCode' => 'ONESELF'
                ],
                'id' => [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ]
            ]
        ];

        // 1.1.3 - participant - Care giver (Mother, Father, Guardian, ect.)
        // [0..*] Zero or more
        // This participant identifies individuals who support the patient such as a relative or caregiver.
        $careOfPlan['participant'] = [
            '@attributes' => [
                'typeCode' => 'IND'
            ],
            'functionCode' => [
                '@attributes' => [
                    'code' => '407543004',
                    'displayName' => 'Primary Carer',
                    'codeSystem' => '2.16.840.1.113883.6.96',
                    'codeSystemName' => 'SNOMED-CT'
                ]
            ],
            'associatedEntity' => [
                '@attributes' => [
                    'classCode' => 'CAREGIVER'
                ],
                'code' => [
                    '@attributes' => [
                        'code' => '', // TODO: Take this information from Patient Contacts
                        'codeSystem' => '2.16.840.1.113883.5.111'
                    ]
                ],
                'addr' => [
                    'streetAddressLine' => '', // TODO: Take this information from Patient Contacts
                    'city' => '', // TODO: Take this information from Patient Contacts
                    'state' => '', // TODO: Take this information from Patient Contacts
                    'postalCode' => '', // TODO: Take this information from Patient Contacts
                    'country' => '' // TODO: Take this information from Patient Contacts
                ],
                'telecom' => [
                    'value' => '', // TODO: Take this information from Patient Contacts
                    'use' => '' // TODO: Take this information from Patient Contacts
                ],
                'associatedPerson' => [
                    'name' => [
                        'prefix' => '', // TODO: Take this information from Patient Contacts
                        'given' => '', // TODO: Take this information from Patient Contacts
                        'family' => '' // TODO: Take this information from Patient Contacts
                    ]
                ]
            ]
        ];

        // 1.1.4 - documentationOf
        // [1..1] Only one
        // The documentationOf relationship in a Care Plan contains the representation of providers who are
        // wholly or partially responsible for the safety and well-being of a subject of care.
        $careOfPlan['documentationOf'] = [
            'serviceEvent' => [
                '@attributes' => [
                    'classCode' => 'PCPR'
                ],
                'effectiveTime' => '' // TODO: ??? Don't know what date will be.
            ]
        ];

        // 1.1.5 - performer
        // [1..*] - Multiple entries
        // The performer(s) represents the healthcare providers involved in the current or historical care of
        // the patient.The patient’s key healthcare providers would be listed here which would include the
        // primary physician and any active consulting physicians, therapists, counselors, and care team members.
        $careOfPlan['performer'] = [
            '@attributes' => [
                'typeCode' => 'PRF'
            ],
            'time' => [
                '@attributes' => [
                    'value' => '' // TODO: ??? Don't know what date will be.
                ]
            ],
            'assignedEntity' => [
                'id' => [
                    '@attributes' => [
                        'extension' => '', // TODO: What value ???
                        'root' => UUID::v4()
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => '59058001',
                        'codeSystem' => '2.16.840.1.113883.6.96',
                        'codeSystemName' => 'SNOMED CT',
                        'displayName' => 'General Physician'
                    ]
                ],
                'addr' => [
                    'streetAddressLine' => '', // TODO: Take this information from provider
                    'city' => '', // TODO: Take this information from provider
                    'state' => '', // TODO: Take this information from provider
                    'postalCode' => '', // TODO: Take this information from provider
                    'country' => '' // TODO: Take this information from provider
                ],
                'telecom' => [
                    'value' => '', // TODO: Take this information from provider
                    'use' => '' // TODO: Take this information from provider
                ],
                'associatedPerson' => [
                    'name' => [
                        'prefix' => '', // TODO: Take this information from provider
                        'given' => '', // TODO: Take this information from provider
                        'family' => '' // TODO: Take this information from provider
                    ]
                ]
            ]
        ];

        // 1.1.6 - relatedDocument
        // [0..1] Zero or more
        // The Care Plan is continually evolving and dynamic. The Care Plan CDA instance is NOT dynamic.
        // Each time a Care Plan CDA is generated it represents a snapshot in time of the Care Plan at that moment.
        // Whenever a care provider or patient generates a Care Plan, it should be noted through relatedDocument
        // whether the current Care Plan replaces or appends another Care Plan. The relatedDocumentTypeCode
        // indicates whether the current document is an addendum to the ParentDocument (APND (append)) or the
        // current document is a replacement of the ParentDocument (RPLC (replace)).
        $careOfPlan['relatedDocument'] = [
            '@attributes' => [
                'typeCode' => 'RPLC'
            ],
            'parentDocument' => [
                'id' => [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => 'CarePlan-X',
                        'codeSystem' => '2.16.840.1.113883.6.1',
                        'codeSystemName' => 'LOINC',
                        'displayName' => 'Care Plan'
                    ]
                ],
                'setId' => [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ],
                'versionNumber' => [
                    '@attributes' => [
                        'value' => '1'
                    ]
                ]
            ]
        ];

        // 1.1.7 - structuredBody
        //
        // * 2.22 - Health Concerns Section (NEW)
        // [1..1] Only one
        // The Health Concerns section contains data that describes an interest or worry about a health state or
        // process that has the potential to require attention, intervention or management.
        //
        // * Goals Section (NEW)
        // [1..1] Only one
        // This template represents patient Goals.  A goal is a defined outcome or condition to be achieved in
        // the process of patient care. Goals include patient-defined goals (e.g., alleviation of health concerns,
        // positive outcomes from interventions, longevity, function, symptom management, comfort) and
        // clinician-specific goals to achieve desired and agreed upon outcomes.
        //
        // * Interventions Section (V2)
        // [1..1] Only one
        // This template represents Interventions.  Interventions are actions taken to maximize the prospects of
        // achieving the patient’s or provider’s goals of care, including the removal of barriers to success.
        // Interventions can be planned, ordered, historical, etc.
        //
        // Interventions include actions that may be ongoing (e.g. maintenance medications that the patient is taking,
        // or monitoring the patient’s health status or the status of an intervention).
        //
        // Instructions are a subset of interventions and may include self-care instructions.
        // Instructions are information or directions to the patient and other providers including how to care
        // for the individual’s condition, what to do at home, when to call for help, any additional appointments,
        // testing, and changes to the medication list or medication instructions, clinical guidelines and a
        // summary of best practice.
        //
        // * Health Status Evaluations/Outcomes Section (NEW)
        // [1..1] Only one
        // This template contains observations regarding the outcome of care resulting from the interventions used to
        // treat the patient. These observations represent status, at points in time, related to established
        // care plan goals and/or interventions.
        $careOfPlan['structuredBody'] = [];


        // * 2.22 - Health Concerns Section (NEW)
        // [1..1] Only one
        // The Health Concerns section contains data that describes an interest or worry about a health state or
        // process that has the potential to require attention, intervention or management.
        $structuredBody_Section['section'] = [
            'templateId' => [
                'root' => '2.16.840.1.113883.10.20.22.2.58'
            ],
            'code' => [
                '@attributes' => [
                    'code' => '46030-3',
                    'displayName' => 'Health Conditions Section',
                    'codeSystem' => '2.16.840.1.113883.6.1',
                    'codeSystemName' => 'LOINC'
                ],
                'title' => 'Health Concerns Section',
                'text' => '',
                // 3.40	- Health Status Observation (V2)
                // This template represents  information about the overall health status of the patient.
                // To represent the impact of a specific problem or concern related to the patient's expected
                // health outcome use the Prognosis Observation Template 2.16.840.1.113883.10.20.22.4.113.
                'entry' => [
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.5'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '11323-3',
                                'codeSystem' => '2.16.840.1.113883.6.1',
                                'codeSystemName' => 'LOINC',
                                'displayName' => 'Health status'
                            ]
                        ],
                        'text' => [
                            'reference' => [
                                '@attributes' => [
                                    'value' => '"#healthstatus' // Narrated Health Status Observation
                                ]
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ],
                        // Value Set: 193. HealthStatus (V2) 2.16.840.1.113883.1.11.20.12.2
                        // Represents the general health status of the patient.
                        // Only one [1..1]
                        //
                        // Code	        Code System	Print Name
                        // 81323004     SNOMED CT	Alive and well
                        // 313386006	SNOMED CT	In remission
                        // 162467007	SNOMED CT	Symptom free
                        // 161901003	SNOMED CT	Chronically ill
                        // 271593001	SNOMED CT	Severely ill
                        // 21134002     SNOMED CT	Disabled
                        // 161045001	SNOMED CT	Severely disabled
                        // 135818000	SNOMED CT	General health poor
                        // 135815002	SNOMED CT	General health good
                        // 135816001	SNOMED CT	General health excellent
                        // TODO: May be we need to modify the database and GaiaEHR to support this code
                        'value' => [
                            '@attributes' => [
                                'xsi:type' => 'CD',
                                'code' => '81323004',
                                'codeSystem' => '2.16.840.1.113883.6.96',
                                'codeSystemName' => 'SNOMED CT',
                                'displayName' => 'Alive and well'
                            ]
                        ]
                    ]
                ],
                // 2.21	Goals Section
                //This template represents patient Goals.  A goal is a defined outcome or condition to be achieved
                // in the process of patient care. Goals include patient-defined goals (e.g., alleviation of health
                // concerns, positive outcomes from interventions, longevity, function, symptom management, comfort)
                // and clinician-specific goals to achieve desired and agreed upon outcomes.
                'section' => [
                    'templateId' => [
                        '@attributes' => [
                            'root' => '2.16.840.1.113883.10.20.22.2.60'
                        ]
                    ],
                    'code' => [
                        'code' => '61146-7',
                        'displayName' => 'Goals',
                        'codeSystem' => '2.16.840.1.113883.6.1',
                        'codeSystemName' => 'LOINC'
                    ],
                    'title' => 'Goals Section',
                    'text' => '', // TODO: Narrative Goal Section
                    'entry' => [
                        // 3.37	Goal Observation
                        // This template represents a patient care goal.  A Goal Observation template may have related
                        // components that are acts, encounters, observations, procedures, substance administrations
                        // or supplies.
                        //
                        // A goal may be a patient or provider goal.  If the author is set to the
                        // recordTarget (patient), this is a patient goal.  If the author is set to a provider,
                        // this is a provider goal. If both patient and provider are set as authors, this is a
                        // negotiated goal.
                        //
                        // A goal usually has a related health concern and/or risk.
                        //
                        // A goal can have components consisting of other goals (milestones), these milestones are
                        // related to the overall goal through entryRelationships.
                        'observation' => [
                            '@attributes' => [
                                'classCode' => 'OBS',
                                'moodCode' => 'GOL'
                            ],
                            'templateId' => [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.44.2'
                                ]
                            ],
                            'templateId' => [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.121'
                                ]
                            ],
                            'id' => [
                                '@attributes' => [
                                    'root' => UUID::v4()
                                ]
                            ],
                            'code' => [
                                '@attributes' => [
                                    'code' => '252465000', // TODO: Code for the Goal Observation
                                    'codeSystem' => '2.16.840.1.113883.6.96',
                                    'codeSystemName' => 'SNOMED CT',
                                    'displayName' => 'Pulse oximetry' // TODO: Name for the Goal Observation
                                ]
                            ],
                            'statusCode' => [
                                '@attributes' => [
                                    'code' => 'active' // TODO: This should be the status of the GOAL
                                ]
                            ],
                            'effectiveTime' => [
                                '@attributes' => [
                                    'value' => '20130902' // TODO: This should be the starting date of the Goal
                                ]
                            ],
                            'value' => [
                                '@attributes' => [
                                    'xsi:type' => 'IVL_PQ' // TODO: This should be the coding for the measurement
                                ],
                                'low' => [
                                    '@attributes' => [
                                        'value' => '92', // TODO: This should be the Goal value
                                        'unit' => '%' // TODO: This should be the Goal value unit
                                    ]
                                ]
                            ],
                            // If the author is set to the recordTarget (patient), this is a patient goal.
                            // If the author is set to a provider, this is a provider goal.
                            // If both patient and provider are set as authors, this is a negotiated goal.
                            'author' => [
                                '@attributes' => [
                                    'typeCode' => 'AUT'
                                ],
                                'templateId' => [
                                    '@attributes' => [
                                        'root' => '2.16.840.1.113883.10.20.22.4.119'
                                    ]
                                ],
                                'time' => [
                                    '@attributes' => [
                                        'value' => '20130730' // TODO: This should be the when the goal was established
                                    ]
                                ],
                                'assignedAuthor' => [
                                    'id' => UUID::v4(),
                                    'code' => [
                                        'code' => '163W00000X', // TODO: This should be the care provider
                                        'displayName' => 'Registered nurse', // TODO: This should be the care provider
                                        'codeSystem' => '2.16.840.1.113883.6.101', // TODO: This should be the care provider
                                        'codeSystemName' => 'Health Care Provider Taxonomy' // TODO: This should be the care provider
                                    ],
                                    'assignedPerson' => [
                                        'name' => [
                                            'given' => 'Nurse', // TODO: This should by the care provider name
                                            'family' => 'Florence', // TODO: This should by the care provider name
                                            'suffix' => 'RN' // TODO: This should by the care provider name
                                        ]
                                    ]
                                ]
                            ],
                            'author' => [
                                '@attributes' => [
                                    'typeCode' => 'AUT'
                                ],
                                'templateId' => [
                                    '@attributes' => [
                                        'root' => '2.16.840.1.113883.10.20.22.4.119'
                                    ]
                                ],
                                'time' => '',
                                'assignedAuthor' => [
                                    // TODO: This should be a pointer back to the patient ID, so you don't have to put
                                    // all the information about the patient all over again.
                                    'id' => ''
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

    }

    /**
     * Method setPlanOfCareSection() TODO
     */
    public function setPlanOfCareSection() {

        // Table moodCode Values
        // -----------------------------------------------------------------------
        // Code             | Definition
        // -----------------------------------------------------------------------
        // EVN (event)      | The entry defines an actual occurrence of an event.
        // INT (intent)     | The entry is intended or planned.
        // PRMS (promise)   | A commitment to perform the stated entry.
        // PRP (proposal)   | A proposal that the stated entry be performed.
        // RQO (request)    | A request or order to perform the stated entry.
        // -----------------------------------------------------------------------
        $Orders = new Orders();
        $planOfCareData['OBS'] = $Orders->getOrderWithoutResultsByPid($this->pid);

        $planOfCareData['ACT'] = [];

        // Get the Encounters
        $planOfCareData['ENC'] = [];

        // Get referrals
        $Referrals = new Referrals();
        $planOfCareData['REF'] = $Referrals->getPatientReferrals(['pid' => $this->pid]);

        $CarePlanGoals = new CarePlanGoals();
        $planOfCareData['PROC'] = $CarePlanGoals->getPatientCarePlanGoalsByPid($this->pid);

        $Appointments = new AppointmentRequest();
        $planOfCareData['APPOINTMENTS'] = $Appointments->getAppointmentRequests(['pid' => $this->pid]);

        /// TODO marroneo!
        if($this->isExcluded('diagnostic_test_pending')){
	        $planOfCareData['OBS'] = [];
        }
	    /// TODO marroneo!
	    if($this->isExcluded('future_appointments')){
		    $planOfCareData['APPOINTMENTS'] = [];
	    }

	    /// TODO marroneo!
	    if($this->isExcluded('future_schedule_test')){
		    $planOfCareData['OBS'] = [];
	    }


        $hasData = !empty($planOfCareData['OBS']) ||
            !empty($planOfCareData['ACT']) ||
            !empty($planOfCareData['ENC']) ||
            !empty($planOfCareData['PROC']);

        if($this->isExcluded('planofcare') || !$hasData){
            $planOfCare['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }

        $planOfCare['templateId'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.2.10'
            ]
        ];
        $planOfCare['code'] = [
            '@attributes' => [
                'code' => '18776-5',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $planOfCare['title'] = 'Plan of Care';
        $planOfCare['text'] = '';


        if($this->isExcluded('planofcare')) {
            $this->addSection(['section' => $planOfCare]);
            return;
        };

        // if one of these are not empty
        if($hasData){
            $planOfCare['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'Planned Activity'
                                    ],
                                    [
                                        '@value' => 'Planned Date'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $planOfCare['text']['table']['tbody']['tr'] = [];
            $planOfCare['entry'] = [];

            // Referrals
            if(!$this->isExcluded('referrals_other_providers')) {
                if (isset($planOfCareData['REF'])) {
                    $ReferringProvider = new ReferringProviders();
                    foreach ($planOfCareData['REF'] as $referral) {
                        // Find the complete information of the refer provider
	                    $referringProvider = $ReferringProvider->getReferringProvider(
                            [
                                'id' => $referral['refer_to']
                            ]
                        );

                        $text = 'Referral: ' . $this->clean($referral['referal_reason']) ;

                        $text .= ' -- ';

	                    $text .= $referringProvider['title'] . ' ' . $referringProvider['fname'] . ' ' . $referringProvider['lname'] . ' ';

	                    if(is_array($referringProvider['facilities']) && isset($referringProvider['facilities'][0])){

		                    if($referringProvider['facilities'][0]['phone_number'] != ''){
			                    $text .= 'Tel: ' . $referringProvider['facilities'][0]['phone_number'] . ' ';
		                    }
		                    if($referringProvider['facilities'][0]['name'] != '') {
			                    $text .= 'Name: ' . $referringProvider['facilities'][0]['name'] . ' ';
		                    }
		                    if($referringProvider['facilities'][0]['address'] != '') {
			                    $text .= 'Address: ' . $referringProvider['facilities'][0]['address'] . ' ';
		                    }
		                    if($referringProvider['facilities'][0]['address_cont'] != '') {
			                    $text .= $referringProvider['facilities'][0]['address_cont'] . ', ';
		                    }
		                    if($referringProvider['facilities'][0]['city'] != '') {
			                    $text .= $referringProvider['facilities'][0]['city'] . ' ';
		                    }
		                    if($referringProvider['facilities'][0]['state'] != '') {
			                    $text .= $referringProvider['facilities'][0]['state'] . ' ';
		                    }
		                    if($referringProvider['facilities'][0]['postal_code'] != '') {
			                    $text .= $referringProvider['facilities'][0]['postal_code'];
		                    }
	                    }

                        // Human readable data
                        $planOfCare['text']['table']['tbody']['tr'][] = [
                            'td' => [
                                [
                                    '@value' => $text
                                ],
                                [
                                    '@value' => date('F j, Y', strtotime($referral['referral_date']))
                                ]
                            ]
                        ];
                        // Tabulated data XML wise
                        $planOfCare['entry'][] = [
                            'act' => [
                                '@attributes' => [
                                    'moodCode' => 'RQO',
                                    'classCode' => 'ACT'
                                ],
                                'templateId' => [
                                    '@attributes' => [
                                        'root' => '2.16.840.1.113883.10.20.22.4.39'
                                    ]
                                ],
                                'id' => [
                                    '@attributes' => [
                                        'root' => UUID::v4()
                                    ]
                                ],
                                'code' => [
                                    '@attributes' => [
                                        'displayName' => $this->clean($referral['referal_reason']),
                                        'codeSystem' => '2.16.840.1.113883.6.96',
                                        'code' => 'NA'
                                    ]
                                ],
                                'statusCode' => [
                                    '@attributes' => [
                                        'code' => '1'
                                    ]
                                ],
                                'effectiveTime' => [
                                    'low' => [
                                        '@attributes' => [
                                            'value' => $this->parseDate($referral['referral_date'])
                                        ]
                                    ],
                                    'high' => [
                                        '@attributes' => [
                                            'value' => $this->parseDate($referral['referral_date'])
                                        ]
                                    ]
                                ]
                            ]
                        ];
                        $planOfCare['entry'][] = [
                            'act' => [
                                '@attributes' => [
                                    'moodCode' => 'RQO',
                                    'classCode' => 'ACT'
                                ],
                                'templateId' => [
                                    '@attributes' => [
                                        'root' => '2.16.840.1.113883.10.20.22.4.41'
                                    ]
                                ],
                                'id' => [
                                    '@attributes' => [
                                        'root' => UUID::v4()
                                    ]
                                ],
                                'code' => [
                                    '@attributes' => [
                                        'displayName' => $this->clean($referral['referal_reason']),
                                        'codeSystem' => '2.16.840.1.113883.6.96',
                                        'code' => 'NA'
                                    ]
                                ],
                                'statusCode' => [
                                    '@attributes' => [
                                        'code' => '1'
                                    ]
                                ],
                                'effectiveTime' => [
                                    'low' => [
                                        '@attributes' => [
                                            'value' => $this->parseDate($referral['referral_date'])
                                        ]
                                    ],
                                    'high' => [
                                        '@attributes' => [
                                            'value' => $this->parseDate($referral['referral_date'])
                                        ]
                                    ]
                                ]
                            ]
                        ];
                    }
                }
            }

            // Observations
            foreach($planOfCareData['OBS'] as $item){
                $planOfCare['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($item['description']) ? 'Observation: ' . $this->clean($item['description']) : ''
                        ],
                        [
                            '@value' => date('F j, Y', strtotime($item['date_ordered']))
                        ]
                    ]
                ];
                $planOfCare['entry'][] = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'RQO'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.44'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => $item['code'],
                                'codeSystemName' => $item['code_type'],
                                'codeSystem' => $this->codes($item['code_type']),
                                'displayName' => $this->clean($item['description'])
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'new'
                            ]
                        ],
                        'effectiveTime' => [
                            'center' => [
                                '@attributes' => [
                                    'value' => $this->parseDate($item['date_ordered'])
                                ]
                            ]
                        ]
                    ]
                ];
            }

            // Appointments
            if(!$this->isExcluded('future_appointments')) {
                foreach ($planOfCareData['APPOINTMENTS'] as $item) {

                    $planOfCare['text']['table']['tbody']['tr'][] = [
                        'td' => [
                            [
                                '@value' => 'Appointments: ' . $this->clean($item['notes'])
                            ],
                            [
                                '@value' => date('F j, Y', strtotime($item['requested_date']))
                            ]
                        ]
                    ];
                    $planOfCare['entry'][] = [
                        'procedure' => [
                            '@attributes' => [
                                'moodCode' => 'RQO',
                                'classCode' => 'ACT'
                            ],
                            'templateId' => [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.41.2'
                                ]
                            ],
                            'id' => [
                                '@attributes' => [
                                    'root' => UUID::v4()
                                ]
                            ],
                            'code' => [
                                '@attributes' => [
                                    'code' => '281189005',
                                    'codeSystemName' => '2.16.840.1.113883.6.96',
                                    'codeSystem' => 'SNOMED-CT',
                                    'displayName' => $this->clean($item['notes']),
                                ]
                            ],
                            'statusCode' => [
                                '@attributes' => [
                                    'code' => 'Active'
                                ]
                            ],
                            'effectiveTime' => [
                                'center' => [
                                    '@attributes' => [
                                        'value' => $this->parseDate($item['requested_date'])
                                    ]
                                ]
                            ]
                        ]
                    ];
                }
            }

            /**
             * Procedures...
             */
            foreach($planOfCareData['PROC'] as $item){
                $planOfCare['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => 'Goal: '.$this->clean($item['goal']).', Instructions:'.$this->clean($item['instructions'])
                        ],
                        [
                            '@value' => date('F j, Y', strtotime($item['plan_date']))
                        ]
                    ]
                ];

                $planOfCare['entry'][] = [
                    'procedure' => [
                        '@attributes' => [
                            'moodCode' => 'RQO',
                            'classCode' => 'PROC'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.41'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => $item['goal_code'],
                                'codeSystemName' => $item['goal_code_type'],
                                'codeSystem' => $this->codes($item['goal_code_type']),
                                'displayName' => $this->clean($item['goal']),
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'new'
                            ]
                        ],
                        'effectiveTime' => [
                            'center' => [
                                '@attributes' => [
                                    'value' => $this->parseDate($item['plan_date'])
                                ]
                            ]
                        ]
                    ]
                ];
            }
        }

        if($this->requiredPlanOfCare || isset($planOfCare['entry'])){
            $this->addSection(['section' => $planOfCare]);
        }
        unset($planOfCareData, $planOfCare);
    }

    /**
     * Method setProblemsSection()
     *
     * This section lists and describes all relevant clinical problems at the time the document is generated.
     * At a minimum, all pertinent current and historical problems should be listed.  Overall health status may
     * be represented in this section.
     */
    public function setProblemsSection() {

        $ActiveProblems = new ActiveProblems();
        $problemsData = $ActiveProblems->getPatientAllProblemsByPid($this->pid);

        $EncounterDiagnostics = new Encounter();
//        $diagnosticsData = [];
        $params = new stdClass();
        $params->filter[0] = new stdClass();
//        if(is_numeric($this->eid)){
////            $params->filter[0]->property = 'eid';
////            $params->filter[0]->value = $this->eid;
////            $diagnosticsData = $EncounterDiagnostics->getEncounterDxs($params);
////            if(!empty($diagnosticsData)){
////                $tempEncounter = $EncounterDiagnostics->getEncounter($this->eid, false, false);
////                $diagnosticsData['encounter'] = $tempEncounter['encounter'];
////            }
//        } elseif($this->eid == 'all_enc') {
////            $params->filter[0]->property = 'pid';
////            $params->filter[0]->value = $this->pid;
////            $diagnosticsData = $EncounterDiagnostics->getEncounterDxs($params);
////            foreach($diagnosticsData as $index => $diagnostic){
////                $tempEncounter = $EncounterDiagnostics->getEncounter($diagnostic['eid'], false, false);
////                $diagnosticsData[$index]['encounter'] = $tempEncounter['encounter'];
////            }
//        }


        if($this->isExcluded('problems') || empty($problemsData)) {
            $problems['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
            $problems['templateId'][] = [
                '@attributes' => [
                    'root' => $this->requiredProblems ? '2.16.840.1.113883.10.20.22.2.5.1' : '2.16.840.1.113883.10.20.22.2.5'
                ]
            ];
            $problems['templateId'][] = [
                '@attributes' => [
                    'root' => '2.16.840.1.113883.3.88.11.83.103'
                ]
            ];

            $problems['code'] = [
                '@attributes' => [
                    'code' => '11450-4',
                    'codeSystemName' => 'LOINC',
                    'codeSystem' => '2.16.840.1.113883.6.1'
                ]
            ];
            $problems['title'] = 'Problems';
            $problems['text'] = '';
            $this->addSection(['section' => $problems]);
            return;
        }

        unset($ActiveProblems, $EncounterDiagnostics);



        // List patient problems.
        if(!empty($problemsData)) {
	        $problems['templateId'][] = [
		        '@attributes' => [
			        'root' => $this->requiredProblems ? '2.16.840.1.113883.10.20.22.2.5.1' : '2.16.840.1.113883.10.20.22.2.5'
		        ]
	        ];
	        $problems['templateId'][] = [
		        '@attributes' => [
			        'root' => '2.16.840.1.113883.3.88.11.83.103'
		        ]
	        ];

	        $problems['code'] = [
		        '@attributes' => [
			        'code' => '11450-4',
			        'codeSystemName' => 'LOINC',
			        'codeSystem' => '2.16.840.1.113883.6.1'
		        ]
	        ];
	        $problems['title'] = 'Problems';
	        $problems['text'] = [
		        'table' => [
			        '@attributes' => [
				        'border' => '1',
				        'width' => '100%'
			        ],
			        'thead' => [
				        'tr' => [
					        [
						        'th' => [
							        [
								        '@value' => 'Condition'
							        ],
							        [
								        '@value' => 'Effective Dates'
							        ],
							        [
								        '@value' => 'Condition Status'
							        ]
						        ]
					        ]
				        ]
			        ],
			        'tbody' => [
				        'tr' => []
			        ]
		        ]
	        ];
        }

	    if(!empty($problemsData)) {

            foreach($problemsData as $item){

                $dateText = $this->parseDate($item['begin_date']) . ' - ';
                if(isset($item['end_date']) && $item['end_date'] != '0000-00-00')
                    $dateText .= $this->parseDate($item['end_date']);

                $problems['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($item['code_text']) ? 'Problem: '.$item['code_text'] : ''
                        ],
                        [
                            '@value' => $dateText
                        ],
                        [
                            '@value' => isset($item['status']) ? $item['status'] : ''
                        ]
                    ]

                ];

                $entry = [
                    'act' => [
                        '@attributes' => [
                            'classCode' => 'ACT',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.3'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => 'CONC',
                                'codeSystemName' => 'ActClass',
                                'codeSystem' => '2.16.840.1.113883.5.6',
                                'displayName' => 'Concern'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                // active ||  suspended ||  aborted ||  completed
                                'code' => 'active'
                            ]
                        ]
                    ]
                ];

                $entry['act']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];
                $entry['act']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'value' => $this->parseDate($item['begin_date'])
                    ]
                ];
                if(isset($item['end_date']) && $item['end_date'] != '0000-00-00'){
                    $entry['act']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } else {
                    $entry['act']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'NI'
                        ]
                    ];
                }

                $entry['act']['entryRelationship'] = [
                    '@attributes' => [
                        'typeCode' => 'SUBJ'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.4'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        // 404684003    SNOMEDCT    Finding
                        // 409586006    SNOMEDCT    Complaint
                        // 282291009    SNOMEDCT    Diagnosis
                        // 64572001     SNOMEDCT    Condition
                        // 248536006    SNOMEDCT    Functional limitation
                        // 418799008    SNOMEDCT    Symptom
                        // 55607006     SNOMEDCT    Problem
                        // 373930000    SNOMEDCT    Cognitive function finding
                        'code' => [
                            '@attributes' => [
                                'code' => '55607006',
                                'displayName' => 'Problem',
                                'codeSystemName' => 'SNOMED CT',
                                'codeSystem' => '2.16.840.1.113883.6.96'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                $entry['act']['entryRelationship']['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];
                $entry['act']['entryRelationship']['observation']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'value' => $this->parseDate($item['begin_date'])
                    ]
                ];
                if(isset($item['end_date']) && $item['end_date'] != '0000-00-00'){
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } else {
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'NI'
                        ]
                    ];
                }
                $entry['act']['entryRelationship']['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CD',
                        'code' => $item['code'],
                        'codeSystemName' => $item['code_type'],
                        'codeSystem' => $this->codes($item['code_type'])
                    ]
                ];
                $problems['entry'][] = $entry;
                unset($entry);
            }
        }

//        // List encounter diagnostic.
//        if(!empty($diagnosticsData)){
//
//            foreach($diagnosticsData as $item){
//
//                $dateText = $this->parseDate($item['encounter']['service_date']) . ' - ';
//                if(isset($item['encounter']['close_date']) && $item['encounter']['close_date'] != '0000-00-00')
//                    $dateText .= $this->parseDate($item['encounter']['close_date']);
//
//                $problems['text']['table']['tbody']['tr'][] = [
//                    'td' => [
//                        [
//                            '@value' => isset($item['code_text']) ? 'Diagnostic: '.$item['code_text'] : ''
//                        ],
//                        [
//                            '@value' => $dateText
//                        ],
//                        [
//                            '@value' => 'Completed'
//                        ]
//                    ]
//
//                ];
//
//                $entry = [
//                    'act' => [
//                        '@attributes' => [
//                            'classCode' => 'ACT',
//                            'moodCode' => 'EVN'
//                        ],
//                        'templateId' => [
//                            '@attributes' => [
//                                'root' => '2.16.840.1.113883.10.20.22.4.3'
//                            ]
//                        ],
//                        'id' => [
//                            '@attributes' => [
//                                'root' => UUID::v4()
//                            ]
//                        ],
//                        'code' => [
//                            '@attributes' => [
//                                'code' => 'CONC',
//                                'codeSystemName' => 'ActClass',
//                                'codeSystem' => '2.16.840.1.113883.5.6',
//                                'displayName' => 'Concern'
//                            ]
//                        ],
//                        'statusCode' => [
//                            '@attributes' => [
//                                // active ||  suspended ||  aborted ||  completed
//                                'code' => 'active'
//                            ]
//                        ]
//                    ]
//                ];
//
//                $entry['act']['effectiveTime'] = [
//                    '@attributes' => [
//                        'xsi:type' => 'IVL_TS'
//                    ]
//                ];
//                $entry['act']['effectiveTime']['low'] = [
//                    '@attributes' => [
//                        'value' => $this->parseDate($item['encounter']['service_date'])
//                    ]
//                ];
//                if(isset($item['encounter']['close_date']) && $item['encounter']['close_date'] != '0000-00-00'){
//                    $entry['act']['effectiveTime']['high'] = [
//                        '@attributes' => [
//                            'value' => $this->parseDate($item['encounter']['close_date'])
//                        ]
//                    ];
//                } else {
//                    $entry['act']['effectiveTime']['high'] = [
//                        '@attributes' => [
//                            'nullFlavor' => 'NI'
//                        ]
//                    ];
//                }
//
//                $entry['act']['entryRelationship'] = [
//                    '@attributes' => [
//                        'typeCode' => 'SUBJ'
//                    ],
//                    'observation' => [
//                        '@attributes' => [
//                            'classCode' => 'OBS',
//                            'moodCode' => 'EVN'
//                        ],
//                        'templateId' => [
//                            '@attributes' => [
//                                'root' => '2.16.840.1.113883.10.20.22.4.4'
//                            ]
//                        ],
//                        'id' => [
//                            '@attributes' => [
//                                'root' => UUID::v4()
//                            ]
//                        ],
//                        'code' => [
//                            '@attributes' => [
//                                'code' => '282291009',
//                                'displayName' => 'Diagnosis',
//                                'codeSystemName' => 'SNOMED CT',
//                                'codeSystem' => '2.16.840.1.113883.6.96'
//                            ]
//                        ],
//                        'statusCode' => [
//                            '@attributes' => [
//                                'code' => 'completed'
//                            ]
//                        ]
//                    ]
//                ];
//
//                $entry['act']['entryRelationship']['observation']['effectiveTime'] = [
//                    '@attributes' => [
//                        'xsi:type' => 'IVL_TS'
//                    ]
//                ];
//                $entry['act']['entryRelationship']['observation']['effectiveTime']['low'] = [
//                    '@attributes' => [
//                        'value' => $this->parseDate($item['encounter']['service_date'])
//                    ]
//                ];
//                if(isset($item['encounter']['end_date']) && $item['encounter']['end_date'] != '0000-00-00'){
//                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
//                        '@attributes' => [
//                            'value' => $this->parseDate($item['encounter']['close_date'])
//                        ]
//                    ];
//                } else {
//                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
//                        '@attributes' => [
//                            'nullFlavor' => 'NI'
//                        ]
//                    ];
//                }
//
//                $entry['act']['entryRelationship']['observation']['value'] = [
//                    '@attributes' => [
//                        'xsi:type' => 'CD',
//                        'code' => $item['code'],
//                        'codeSystemName' => $item['code_type'],
//                        'codeSystem' => $this->codes($item['code_type'])
//                    ]
//                ];
//                $problems['entry'][] = $entry;
//                unset($entry);
//            }
//        }

        if($this->requiredProblems || !empty($problems['entry'])){
            $this->addSection(['section' => $problems]);
        }
        unset($problemsData, $problems);
    }

    /**
     * Method setAllergiesSection()
     */
    public function setAllergiesSection() {
        $Allergies = new Allergies();

        $allergiesData = $Allergies->getPatientAllergiesByPid($this->pid);
        unset($Allergies);

        if($this->isExcluded('allergies') || empty($allergiesData)){
            $allergies['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $allergies['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredAllergies ? '2.16.840.1.113883.10.20.22.2.6.1' : '2.16.840.1.113883.10.20.22.2.6'
            ]
        ];
        $allergies['code'] = [
            '@attributes' => [
                'code' => '48765-2',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $allergies['title'] = 'Allergies, Adverse Reactions, Alerts';
        $allergies['text'] = '';

        if($this->isExcluded('allergies')) {
            $this->addSection(['section' => $allergies]);
            return;
        };

        if(!empty($allergiesData)){
            $allergies['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'Substance'
                                    ],
                                    [
                                        '@value' => 'Reaction'
                                    ],
                                    [
                                        '@value' => 'Severity'
                                    ],
                                    [
                                        '@value' => 'Status'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];

            $allergies['entry'] = [];

            foreach($allergiesData as $item){

                $hasBeginDate = preg_match('/^\d{4}-\d{2}-\d{2}/', $item['begin_date']);
                $hasEndDate = preg_match('/^\d{4}-\d{2}-\d{2}/', $item['end_date']);

                $allergies['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($item['allergy']) ? $item['allergy'] : ''
                        ],
                        [
                            '@value' => isset($item['reaction']) ? $item['reaction'] : ''
                        ],
                        [
                            '@value' => isset($item['severity']) ? $item['severity'] : ''
                        ],
                        [
                            '@value' => isset($item['status']) ? $item['status'] : ''
                        ]
                    ]
                ];

                $entry = [
                    'act' => [
                        '@attributes' => [
                            'classCode' => 'ACT',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.30'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '48765-2',
                                'codeSystemName' => 'LOINC',
                                'codeSystem' => '2.16.840.1.113883.6.1'
                            ]
                        ]
                    ]
                ];

                $entry['act']['statusCode'] = [
                    '@attributes' => [
                        // use snomed code for active
                        'code' => $item['status_code'] == '55561003' ? 'active' : 'completed'
                    ]
                ];

                $entry['act']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'value' => $this->parseDate($item['begin_date'])
                    ]
                ];

                if($hasEndDate){
                    $entry['act']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } elseif($entry['act']['statusCode'] == 'completed' && !$hasEndDate) {
                    $entry['act']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                $entry['act']['entryRelationship'] = [
                    '@attributes' => [
                        'typeCode' => 'SUBJ'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.7'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => 'ASSERTION',
                                'codeSystem' => '2.16.840.1.113883.5.4'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                // If it is unknown when the allergy began, this effectiveTime
                // SHALL contain low/@nullFlavor="UNK" (CONF:9103)
                $entry['act']['entryRelationship']['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS',
                    ]
                ];

                if($hasBeginDate){
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['begin_date'])
                        ]
                    ];
                } else {
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                if($hasEndDate){
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } elseif($entry['act']['statusCode'] == 'completed' && !$hasEndDate) {
                    $entry['act']['entryRelationship']['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                // 420134006    SNOMEDCT    Propensity to adverse reactions
                // 418038007    SNOMEDCT    Propensity to adverse reactions to substance
                // 419511003    SNOMEDCT    Propensity to adverse reactions to drug
                // 418471000    SNOMEDCT    Propensity to adverse reactions to food
                // 419199007    SNOMEDCT    Allergy to substance
                // 416098002    SNOMEDCT    Drug allergy
                // 414285001    SNOMEDCT    Food allergy
                // 59037007     SNOMEDCT    Drug intolerance
                // 235719002    SNOMEDCT    Food intolerance
                $entry['act']['entryRelationship']['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CD',
                        'code' => $item['allergy_type_code'],
                        'displayName' => $item['allergy_type'],
                        'codeSystemName' => $item['allergy_type_code_type'],
                        'codeSystem' => $this->codes($item['allergy_type_code_type'])
                    ]
                ];

                $entry['act']['entryRelationship']['observation']['participant'] = [
                    '@attributes' => [
                        'typeCode' => 'CSM'
                    ],
                    'participantRole' => [
                        '@attributes' => [
                            'classCode' => 'MANU'
                        ],
                        'playingEntity' => [
                            '@attributes' => [
                                'classCode' => 'MMAT'
                            ],
                            'code' => [
                                '@attributes' => [
                                    'code' => $item['allergy_code'],
                                    'displayName' => $item['allergy'],
                                    'codeSystemName' => $item['allergy_code_type'],
                                    'codeSystem' => $this->codes($item['allergy_code_type'])
                                ]
                            ]
                        ]
                    ]
                ];

                // Allergy Status Observation
                $entryRelationship = [
                    '@attributes' => [
                        'typeCode' => 'SUBJ',
                        'inversionInd' => 'true'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.28'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '33999-4',
                                'codeSystemName' => 'LOINC',
                                'codeSystem' => '2.16.840.1.113883.6.1'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                $entryRelationship['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];

                if($hasBeginDate){
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['begin_date'])
                        ]
                    ];
                } else {
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'nullFLavor' => 'UNK'
                        ]
                    ];
                }

                if($hasEndDate){
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } elseif($entry['act']['statusCode'] == 'completed' && !$hasEndDate) {
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                $entryRelationship['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CE',
                        'code' => $item['status_code'],
                        'displayName' => $item['status'],
                        'codeSystemName' => $item['status_code_type'],
                        'codeSystem' => $this->codes($item['status_code_type'])
                    ]
                ];

                $entry['act']['entryRelationship']['observation']['entryRelationship'][] = $entryRelationship;
                unset($entryRelationship);

                // Reaction Observation
                $entryRelationship = [
                    '@attributes' => [
                        'typeCode' => 'MFST',
                        'inversionInd' => 'true'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.9'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'nullFlavor' => 'NA'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                $entryRelationship['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS',
                    ]
                ];

                if($hasBeginDate){
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['begin_date'])
                        ]
                    ];
                } else {
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                if($hasEndDate){
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } elseif($entry['act']['statusCode'] == 'completed' && !$hasEndDate) {
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                $entryRelationship['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CD',
                        'code' => $item['reaction_code'],
                        'displayName' => $item['reaction'],
                        'codeSystemName' => $item['reaction_code_type'],
                        'codeSystem' => $this->codes($item['reaction_code_type'])
                    ]
                ];

                $entry['act']['entryRelationship']['observation']['entryRelationship'][] = $entryRelationship;
                unset($entryRelationship);

                // Severity Observation
                $entryRelationship = [
                    '@attributes' => [
                        'typeCode' => 'SUBJ',
                        'inversionInd' => 'true'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.8'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => 'SEV',
                                'codeSystemName' => 'ActCode',
                                'codeSystem' => '2.16.840.1.113883.5.4',
                                'displayName' => 'Severity Observation'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                $entryRelationship['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS',
                    ]
                ];

                if($hasBeginDate){
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['begin_date'])
                        ]
                    ];
                } else {
                    $entryRelationship['observation']['effectiveTime']['low'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                if($hasEndDate){
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['end_date'])
                        ]
                    ];
                } elseif($entry['act']['statusCode'] == 'completed' && !$hasEndDate) {
                    $entryRelationship['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'UNK'
                        ]
                    ];
                }

                $entryRelationship['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CD',
                        'code' => $item['severity_code'],
                        'displayName' => $item['severity'],
                        'codeSystemName' => $item['severity_code_type'],
                        'codeSystem' => $this->codes($item['severity_code_type'])
                    ]
                ];

                $entry['act']['entryRelationship']['observation']['entryRelationship'][] = $entryRelationship;
                unset($entryRelationship);

                $allergies['entry'][] = $entry;

            }
        }
        if($this->requiredAllergies || !empty($allergies['entry'])){
            $this->addSection(['section' => $allergies]);
        }
        unset($allergiesData, $allergies);
    }

    /**
     * Method setSocialHistorySection()
     */
    public function setSocialHistorySection() {

        $SocialHistory = new SocialHistory();

        if($this->isExcluded('social')) {
            $socialHistory = [
                'templateId' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.10.20.22.2.17'
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => '29762-2',
                        'codeSystemName' => 'LOINC',
                        'codeSystem' => '2.16.840.1.113883.6.1',
                        'displayName' => "Social History"
                    ]
                ],
                'title' => 'Social History',
                'text' => 'No Information About Social History',
                'entry' => [
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN',
                            'nullFlavor' => 'NI'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.78'
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => 'ASSERTION',
                                'codeSystem' => '2.16.840.1.113883.5.4'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ],
                        'effectiveTime' => [
                            '@attributes' => [
                                'value' => $this->dateNow
                            ]
                        ],
                        'value' => [
                            '@attributes' => [
                                'code' => '266927001',
                                'codeSystem' => '2.16.840.1.113883.6.96',
                                'codeSystemName' => 'SNOMED CT',
                                'displayName' => 'Unknown if ever smoked',
                                'xsi:type' => 'CD'
                            ]
                        ]
                    ]
                ]
            ];
            $this->addSection(['section' => $socialHistory]);
            return;
        };

        /**
         * Smoking Status Observation - This clinical statement represents a patient's current smoking
         * status. The vocabulary selected for this clinical statement is the best approximation of the
         * statuses in Meaningful Use (MU) Stage 1.
         *
         * If the patient is a smoker (77176002), the effectiveTime/low element must be present. If the patient
         * is an ex-smoker (8517006), both the effectiveTime/low and effectiveTime/high element must be present.
         *
         * The smoking status value set includes a special code to communicate if the smoking status is unknown
         * which is different from how Consolidated CDA generally communicates unknown information.
         */
        $smokingStatus = $SocialHistory->getSocialHistoryByPidAndCode($this->pid, 'smoking_status');
        $socialHistories = $SocialHistory->getSocialHistoryByPidAndCode($this->pid);

        // TODO marroneo!
        if($this->isExcluded('patient_smoking_status')){
	        $smokingStatus = [];
        }

        if(count($smokingStatus) > 0 || count($socialHistories) > 0){
            $socialHistory = [
                'templateId' => [
                    '@attributes' => [
                        'root' => '2.16.840.1.113883.10.20.22.2.17'
                    ]
                ],
                'code' => [
                    '@attributes' => [
                        'code' => '29762-2',
                        'codeSystemName' => 'LOINC',
                        'codeSystem' => '2.16.840.1.113883.6.1',
                        'displayName' => "Social History"
                    ]
                ],
                'title' => 'Social History',
                'text' => [
                    'table' => [
                        '@attributes' => [
                            'border' => '1',
                            'width' => '100%'
                        ],
                        'thead' => [
                            'tr' => [
                                [
                                    'th' => [
                                        [
                                            '@value' => 'Social History Element'
                                        ],
                                        [
                                            '@value' => 'Description'
                                        ],
                                        [
                                            '@value' => 'Effective Dates'
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'tbody' => [
                            'tr' => []
                        ]
                    ]
                ]
            ];

            $smokingStatus = end($smokingStatus);
            $socialHistory['text']['table']['tbody']['tr'][] = [
                'td' => [
                    [
                        '@value' => 'Smoking Status'
                    ],
                    [
                        '@value' => $smokingStatus['status']
                    ],
                    [
                        '@value' => isset($smokingStatus['start_date']) ? date('F j, Y', strtotime($smokingStatus['start_date'])) : ''
                    ]
                ]
            ];

            if(empty($smokingStatus['start_date'])){
                $startDate = [
                    '@attributes' => [
                        'nullFlavor' => 'UNK'
                    ]
                ];
            } else {
                $startDate = [
                    '@attributes' => [
                        'value' => $this->parseDate($smokingStatus['start_date'])
                    ]
                ];
            }

            $socialHistory['entry'][] = [
                '@attributes' => [
                    'typeCode' => 'DRIV'
                ],
                'observation' => [
                    '@attributes' => [
                        'classCode' => 'OBS',
                        'moodCode' => 'EVN'
                    ],
                    'templateId' => [
                        [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.78'
                            ]
                        ],
                        [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.85'
                            ]
                        ],
                        [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.38.2'
                            ]
                        ]
                    ],
                    'code' => [
                        '@attributes' => [
                            'code' => 'ASSERTION',
                            'codeSystemName' => 'ActCode',
                            'codeSystem' => '2.16.840.1.113883.5.4'
                        ]
                    ],
                    'statusCode' => [
                        '@attributes' => [
                            'code' => 'completed'
                        ]
                    ],
                    'effectiveTime' => $startDate,

                    // Code             System      Print Name
                    // 449868002        SNOMEDCT    Current every day smoker
                    // 428041000124106  SNOMEDCT    Current some day smoker
                    // 8517006          SNOMEDCT    Former smoker
                    // 266919005        SNOMEDCT    Never smoker (Never Smoked)
                    // 77176002         SNOMEDCT    Smoker, current status unknown
                    // 266927001        SNOMEDCT    Unknown if ever smoked
                    'value' => [
                        '@attributes' => [
                            'xsi:type' => 'CD',
                            'code' => $smokingStatus['status_code'],
                            'displayName' => $smokingStatus['status'],
                            'codeSystemName' => $smokingStatus['status_code_type'],
                            'codeSystem' => $this->codes($smokingStatus['status_code_type'])
                        ]
                    ]
                ]
            ];

            /**
             * This Social History Observation defines the patient's occupational, personal (e.g., lifestyle),
             * social, and environmental history and health risk factors, as well as administrative data such
             * as marital status, race, ethnicity, and religious affiliation.
             */
            foreach($socialHistories As $socialHistoryEntry){

                $dateText = $this->parseDate($socialHistoryEntry['start_date']) . ' - ';
                if($socialHistoryEntry['end_date'] != '0000-00-00 00:00:00')
                    $dateText .= $this->parseDate($socialHistoryEntry['end_date']);

                $socialHistory['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($socialHistoryEntry['category_code_text']) ? $socialHistoryEntry['category_code_text'] : ''
                        ],
                        [
                            '@value' => isset($socialHistoryEntry['observation']) ? $socialHistoryEntry['observation'] : ''
                        ],
                        [
                            '@value' => $dateText
                        ]
                    ]
                ];

                $entry = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.38'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],

                        // Code            System    Print Name
                        // 229819007    SNOMEDCT    Tobacco use and exposure
                        // 256235009    SNOMEDCT    Exercise
                        // 160573003    SNOMEDCT    Alcohol intake
                        // 364393001    SNOMEDCT    Nutritional observable
                        // 364703007    SNOMEDCT    Employment detail
                        // 425400000    SNOMEDCT    Toxic exposure status
                        // 363908000    SNOMEDCT    Details of drug misuse behavior
                        // 228272008    SNOMEDCT    Health-related behavior
                        // 105421008    SNOMEDCT    Educational Achievement
                        'code' => [
                            '@attributes' => [
                                'code' => $socialHistoryEntry['category_code'],
                                'codeSystem' => $this->codes($socialHistoryEntry['category_code_type']),
                                'codeSystemName' => $socialHistoryEntry['category_code_text'],
                                'displayName' => $socialHistoryEntry['category_code_text']
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

                $entry['observation']['effectiveTime'] = [
                    '@attributes' => [
                        'xsi:type' => 'IVL_TS'
                    ]
                ];

                $entry['observation']['effectiveTime']['low'] = [
                    '@attributes' => [
                        'value' => $this->parseDate($socialHistoryEntry['start_date'])
                    ]
                ];

                if($socialHistoryEntry['end_date'] != '0000-00-00 00:00:00'){
                    $entry['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($socialHistoryEntry['end_date'])
                        ]
                    ];
                } else {
                    $entry['observation']['effectiveTime']['high'] = [
                        '@attributes' => [
                            'nullFlavor' => 'NI'
                        ]
                    ];
                }
                $entry['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'ST'
                    ],
                    '@value' => $socialHistoryEntry['observation']
                ];

                $socialHistory['entry'][] = $entry;
                unset($entry);
            }
        } else {
            $socialHistory['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        unset($smokingStatus);
        unset($socialHistories);

        if(isset($socialHistory)){
            $this->addSection(['section' => $socialHistory]);
            unset($socialHistoryData, $socialHistory);
        }

    }

    /**
     * Method setResultsSection()
     *
     * The Results section contains observations of results generated by laboratories, imaging procedures,
     * and other procedures. These coded result observations are contained within a Results Organizer in
     * the Results Section. The scope includes observations such as
     * hematology, chemistry, serology, virology, toxicology, microbiology, plain x-ray, ultrasound, CT, MRI,
     * angiography, echocardiography, nuclear medicine, pathology, and procedure observations.
     *
     * The section often includes notable results such as abnormal values or relevant trends, and could
     * contain all results for the period of time being documented.
     *
     * Laboratory results are typically generated by laboratories providing analytic services in areas such as
     * chemistry, hematology, serology, histology, cytology, anatomic pathology, microbiology, and/or virology.
     * These observations are based on analysis of specimens obtained from the patient and submitted to the laboratory.
     *
     * Imaging results are typically generated by a clinician reviewing the output of an imaging procedure,
     * such as where a cardiologist reports the left ventricular ejection fraction based on the review of a
     * cardiac echocardiogram.
     *
     * Procedure results are typically generated by a clinician to provide more granular information about
     * component observations made during  a procedure, such as where a gastroenterologist reports the size
     * of a polyp observed during a colonoscopy.
     */
    public function setResultsSection() {

        $Orders = new Orders();
        $resultsData = $Orders->getOrderWithResultsByPid($this->pid);

        $results = [];

        if($this->isExcluded('results') || empty($resultsData)){
            $results['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $results['templateId'] = [
            '@attributes' => [
                'root' => $this->requiredResults ? '2.16.840.1.113883.10.20.22.2.3.1' : '2.16.840.1.113883.10.20.22.2.3'
            ]
        ];
        $results['code'] = [
            '@attributes' => [
                'code' => '30954-2',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $results['title'] = 'Results';
        $results['text'] = '';

        if($this->isExcluded('results')) {
            $this->addSection(['section' => $results]);
            return;
        };

        if(!empty($resultsData)){

            $results['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'tbody' => []
                ]
            ];
            $results['entry'] = [];

            foreach($resultsData as $item){

                $results['text']['table']['tbody'][] = [
                    'tr' => [
                        [
                            'th' => [
                                [
                                    '@value' => 'Results: '.$this->clean($item['description'])
                                ],
                                [
                                    '@value' => $this->parseDateToText($item['result']['result_date'])
                                ]
                            ]
                        ]
                    ]
                ];

                $entry = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'organizer' => [
                        '@attributes' => [
                            // CLUSTER || BATTERY
                            'classCode' => 'CLUSTER',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            '@attributes' => [
                                'root' => '2.16.840.1.113883.10.20.22.4.1'
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => $item['code'],
                                'displayName' => $item['description'],
                                'codeSystemName' => $item['code_type'],
                                'codeSystem' => $this->codes($item['code_type'])
                            ]
                        ],
                        // Code         System      Print Name
                        // aborted      ActStatus   aborted
                        // active       ActStatus   active
                        // cancelled    ActStatus   cancelled
                        // completed    ActStatus   completed
                        // held         ActStatus   held
                        // suspended    ActStatus   suspended
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ],
                        'component' => []
                    ]
                ];

                foreach($item['result']['observations'] as $obs){

                    if($obs['value'] == '')
                        continue;

                    $results['text']['table']['tbody'][] = [
                        'tr' => [
                            [
                                'td' => [
                                    [
                                        '@value' => isset($obs['code_text']) ? $obs['code_text'] : ''
                                    ],
                                    [
                                        '@attributes' => [
                                            'align' => 'left'
                                        ],
                                        '@value' => htmlentities($obs['value'] . ' ' . $obs['units'] . ' [' . $obs['reference_rage'] . ']')
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $component = [
                        'observation' => [
                            '@attributes' => [
                                'classCode' => 'OBS',
                                'moodCode' => 'EVN'
                            ],
                            'templateId' => [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.2'
                                ]
                            ],
                            'id' => [
                                '@attributes' => [
                                    'root' => UUID::v4()
                                ]
                            ],
                            'code' => [
                                '@attributes' => [
                                    'code' => $obs['code'],
                                    'codeSystemName' => $obs['code_type'],
                                    'codeSystem' => $this->codes($obs['code_type']),
                                    'displayName' => $obs['code_text']
                                ]
                            ],

                            // Code         System      Print Name
                            // aborted      ActStatus   aborted
                            // active       ActStatus   active
                            // cancelled    ActStatus   cancelled
                            // completed    ActStatus   completed
                            // held         ActStatus   held
                            // suspended    ActStatus   suspended
                            'statusCode' => [
                                '@attributes' => [
                                    'code' => 'completed'
                                ]
                            ]
                        ]
                    ];

                    $component['observation']['effectiveTime'] = [
                        '@attributes' => [
                            'xsi:type' => 'IVL_TS',
                        ],
                        'low' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['result']['result_date'])
                            ]
                        ],
                        'high' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['result']['result_date'])
                            ]
                        ]
                    ];

                    if(is_numeric($obs['value'])){
                        $component['observation']['value'] = [
                            '@attributes' => [
                                'xsi:type' => 'PQ',
                                'value' => htmlentities($obs['value'])
                            ]
                        ];
                        if($obs['units'] != ''){
                            $component['observation']['value']['@attributes']['unit'] = htmlentities($obs['units']);
                        }
                    } else {
                        $component['observation']['value'] = [
                            '@attributes' => [
                                'xsi:type' => 'ST'
                            ],
                            '@value' => htmlentities($obs['value'])
                        ];
                    }

                    if($obs['abnormal_flag'] != ''){
                        $component['observation']['interpretationCode'] = [
                            '@attributes' => [
                                'code' => htmlentities($obs['abnormal_flag']),
                                'codeSystemName' => 'ObservationInterpretation',
                                'codeSystem' => '2.16.840.1.113883.5.83'
                            ]
                        ];
                    } else {
                        $component['observation']['interpretationCode'] = [
                            '@attributes' => [
                                'nullFlavor' => 'NA'
                            ]
                        ];
                    }

                    $ranges = preg_split("/to|-/", $obs['reference_rage']);
                    if(is_array($ranges) && count($ranges) > 2){
                        $component['observation']['referenceRange'] = [
                            'observationRange' => [
                                'value' => [
                                    '@attributes' => [
                                        'xsi:type' => 'IVL_PQ'
                                    ],
                                    'low' => [
                                        '@attributes' => [
                                            'value' => htmlentities($ranges[0]),
                                            'unit' => htmlentities($obs['units'])
                                        ]
                                    ],
                                    'high' => [
                                        '@attributes' => [
                                            'value' => htmlentities($ranges[1]),
                                            'unit' => htmlentities($obs['units'])
                                        ]
                                    ]
                                ]
                            ]
                        ];

                    } else {
                        $component['observation']['referenceRange']['observationRange']['text'] = [
                            '@attributes' => [
                                'nullFlavor' => 'NA'
                            ]
                        ];
                    }

                    $entry['organizer']['component'][] = $component;

                }

                $results['entry'][] = $entry;
            }

        }

        if($this->requiredResults || !empty($results['entry'])){
            $this->addSection(['section' => $results]);
        }
        unset($resultsData, $results, $order);
    }

    /**
     * Method setFunctionalStatusSection()
     *
     * The Functional Status Section contains observations and assessments of a patient's physical abilities.
     * A patient’s functional status may include information regarding the patient’s general function
     * such as ambulation, ability to perform Activities of Daily Living (ADLs)
     * (e.g., bathing, dressing, feeding, grooming) or Instrumental Activities of Daily Living (IADLs)
     * (e.g., shopping, using a telephone, balancing a check book). Problems that impact function
     * (e.g., dyspnea, dysphagia) can be contained in the section.
     *
     * TODO: Need some finishing...
     */
    public function setFunctionalStatusSection() {

        $CognitiveAndFunctionalStatus = new CognitiveAndFunctionalStatus();
        $functionalStatusData = $CognitiveAndFunctionalStatus->getPatientCognitiveAndFunctionalStatusesByPid($this->pid);

        if(empty($functionalStatusData)){
            $functionalStatus['@attributes'] = [
                'nullFlavor' => 'NI'
            ];
        }
        $functionalStatus['templateId'] = [
            '@attributes' => [
                'root' => '2.16.840.1.113883.10.20.22.2.14'
            ]
        ];
        $functionalStatus['code'] = [
            '@attributes' => [
                'code' => '47420-5',
                'codeSystemName' => 'LOINC',
                'codeSystem' => '2.16.840.1.113883.6.1'
            ]
        ];
        $functionalStatus['title'] = 'Functional status assessment';
        $functionalStatus['text'] = '';

        if(!empty($functionalStatusData)){
            $functionalStatus['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'Functional or Cognitive Finding'
                                    ],
                                    [
                                        '@value' => 'Observation'
                                    ],
                                    [
                                        '@value' => 'Observation Date'
                                    ],
                                    [
                                        '@value' => 'Condition Status'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];
            $functionalStatus['entry'] = [];

            foreach($functionalStatusData as $item){

                $functionalStatus['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => isset($item['category']) ? $item['category'] : ''
                        ],
                        [
                            '@value' => isset($item['code_text']) ? $item['code_text'] : ''
                        ],
                        [
                            '@value' => $this->parseDate($item['created_date'])
                        ],
                        [
                            '@value' => isset($item['status']) ? $item['status'] : ''
                        ]
                    ]

                ];

                $entry = [
                    'observation' => [
                        '@attributes' => [
                            'classCode' => 'OBS',
                            'moodCode' => 'EVN'
                        ],
                    ]
                ];

                $entry['observation']['templateId'] = [
                    '@attributes' => [
                        'root' => ($item['category_code'] == '363871006' ? '2.16.840.1.113883.10.20.22.4.74' : '2.16.840.1.113883.10.20.22.4.67')
                    ]
                ];

                $entry['observation']['id'] = [
                    '@attributes' => [
                        'root' => UUID::v4()
                    ]
                ];

                $entry['observation']['code'] = [
                    '@attributes' => [
                        'code' => $item['category_code'],
                        'codeSystemName' => $item['category_code_type'],
                        'codeSystem' => $this->codes($item['category_code_type']),
                        'displayName' => $item['category']
                    ]
                ];

                $entry['observation']['statusCode'] = [
                    '@attributes' => [
                        'code' => 'completed'
                    ]
                ];

                if(isset($item['begin_date']) && $item['begin_date'] != '0000-00-00'){
                    $entry['observation']['effectiveTime'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($item['created_date'])
                        ]
                    ];
                } elseif(isset($item['end_date']) && $item['end_date'] != '0000-00-00') {
                    $entry['observation']['effectiveTime'] = [
                        '@attributes' => [
                            'xsi:type' => 'IVL_TS'
                        ],
                        'low' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['begin_date'])
                            ]
                        ],
                        'high' => [
                            '@attributes' => [
                                'nullFlavor' => 'NI'
                            ]
                        ]
                    ];
                } else {
                    $entry['observation']['effectiveTime'] = [
                        '@attributes' => [
                            'xsi:type' => 'IVL_TS'
                        ],
                        'low' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['begin_date'])
                            ]
                        ],
                        'high' => [
                            '@attributes' => [
                                'value' => $this->parseDate($item['end_date'])
                            ]
                        ]
                    ];
                }

                $entry['observation']['value'] = [
                    '@attributes' => [
                        'xsi:type' => 'CD',
                        'code' => $item['code'],
                        'codeSystemName' => $item['code_type'],
                        'codeSystem' => $this->codes($item['code_type']),
                        'displayName' => $item['code_text']
                    ]
                ];

                $functionalStatus['entry'][] = $entry;
            }
        } else{

        }

        if($this->requiredResults || !empty($functionalStatus['entry'])){
            $this->addSection(['section' => $functionalStatus]);
        }
        unset($functionalStatusData, $functionalStatus);
    }

    /**
     * Method setEncountersSection()
     * This section lists and describes any healthcare encounters pertinent to the patient’s current health
     * status or historical health history. An encounter is an interaction, regardless of the setting, between a
     * patient and a practitioner who is vested with primary responsibility for diagnosing, evaluating, or
     * treating the patient’s condition. It may include visits, appointments, or non-face-to-face interactions.
     *
     * It is also a contact between a patient and a practitioner who has primary responsibility
     * (exercising independent judgment) for assessing and treating the patient at a given contact.
     * This section may contain all encounters for the time period being summarized, but should
     * include notable encounters.
     */
    public function setEncountersSection() {

        $filters = new stdClass();
        $filters->filter[0] = new stdClass();
        if(is_numeric($this->eid)){
            $filters->filter[0]->property = 'eid';
            $filters->filter[0]->value = $this->eid;
        }elseif($this->eid == 'all_enc'){
            $filters->filter[0]->property = 'pid';
            $filters->filter[0]->value = $this->pid;
        }elseif($this->eid == 'no_enc'){
            return;
        }
        $encountersData = $this->Encounter->getEncounters($filters, false, false);

        $encounters = [
            'templateId' => [
                '@attributes' => [
                    'root' => $this->requiredEncounters ? '2.16.840.1.113883.10.20.22.2.22.2' : '2.16.840.1.113883.10.20.22.2.22'
                ]
            ],
            'code' => [
                '@attributes' => [
                    'code' => '46240-8',
                    'codeSystemName' => 'LOINC',
                    'codeSystem' => '2.16.840.1.113883.6.1'
                ]
            ],
            'title' => 'Encounters'
        ];

	    /**
	     * array of codes used during encounter
	     * no need to use code type
	     * example:
	     * active problems... diagnosis... active medications... etc... etc...
	     * ['F20', '22999', '26345']
	     */
		$codes = [];
        $DecisionAids = new DecisionAids();
	    $decisionAids = $DecisionAids->getDecisionAidsByTriggerCodes($codes);

	    if(empty($decisionAids)) return;

        if(!empty($encountersData)){
            $encounters['text'] = [
                'table' => [
                    '@attributes' => [
                        'border' => '1',
                        'width' => '100%'
                    ],
                    'thead' => [
                        'tr' => [
                            [
                                'th' => [
                                    [
                                        '@value' => 'SNOMED'
                                    ],
                                    [
                                        '@value' => 'Diagnosis / Instructions'
                                    ],
                                    [
                                        '@value' => 'Performer'
                                    ],
                                    [
                                        '@value' => 'Location'
                                    ],
                                    [
                                        '@value' => 'Date'
                                    ],
                                    [
                                        '@value' => 'Status'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'tbody' => [
                        'tr' => []
                    ]
                ]
            ];

	        $Encounter = new Encounter();

            foreach($encountersData as $encounter){

                $providerInfo = $this->User->getUserByUid($encounter['provider_uid']);

	            $excludePatientDecisionAids = $this->isExcluded('patient_decision_aids');
	            $excludeVisitDateLocation = $this->isExcluded('visit_date_location');

	            if($excludeVisitDateLocation){
		            $office_visit_location = '';
		            $office_visit_date['from'] = '';
		            $office_visit_date['to'] = '';
	            }else{
		            $office_visit_location = 'Office visit';
		            $office_visit_date['from'] = $encounter['service_date'];
		            $office_visit_date['to'] = $encounter['service_date'];
	            }

	            $encounter_dxs = $Encounter->getEncounterDxs(['eid' =>  $encounter['eid']]);

	            $encounter_dx_text = [];
				foreach ($encounter_dxs as $encounter_dx){
					$encounter_dx_text[] = $encounter_dx['code'] . ' (' .$encounter_dx['code_type']. ') - ' .  $encounter_dx['code_text'];
				}

	            $encounter_dx_text = implode(', ', $encounter_dx_text);


	            if(!$excludePatientDecisionAids) {

	            	$instruction = 'Decision Aids: ' . (isset($decisionAids[0]['instruction_code_description']) ? $this->clean($decisionAids[0]['instruction_code_description']) : '');

		            // Decision Aids
		            $encounters['text']['table']['tbody']['tr'][] = [
			            'td' => [
				            [
					            '@value' => ''
				            ],
				            [
					            '@value' => $instruction
				            ],
				            [
					            '@value' => $providerInfo['fname'] . ' ' . $providerInfo['mname'] . ' ' . $providerInfo['lname']
				            ],
				            [
					            '@value' => $office_visit_location
				            ],
				            [
					            '@value' => $office_visit_date['from']
				            ],
				            [
					            '@value' => (empty($encounter['close_date']) || $encounter['close_date'] == '0000-00-00') ? 'Active' : 'Inactive'
				            ]
			            ]
		            ];

	            }

                // Visit
                $encounters['text']['table']['tbody']['tr'][] = [
                    'td' => [
                        [
                            '@value' => ''
                        ],
                        [
                            '@value' => 'Visit: '.$encounter['brief_description'] . ' -- Diagnosis: ' . $encounter_dx_text
                        ],
                        [
                            '@value' => $providerInfo['fname'].' '.$providerInfo['mname'].' '.$providerInfo['lname']
                        ],
                        [
                            '@value' => $office_visit_location
                        ],
                        [
                            '@value' => $office_visit_date['from']
                        ],
                        [
                            '@value' => (empty($encounter['close_date']) || $encounter['close_date'] == '0000-00-00') ? 'Active' : 'Inactive'
                        ]
                    ]
                ];

	            if($office_visit_date['from'] == ''){
		            $serviceDate['low'] = [
			            '@attributes' => [
				            'nullFlavor' => 'NI'
			            ]
		            ];
	            }else{
		            $serviceDate['low'] = [
			            '@attributes' => [
				            'value' => $this->parseDate($encounter['service_date'])
			            ]
		            ];
	            }

                if($office_visit_date['to'] != '' && !empty($encounter['close_date'])){
                    $serviceDate['high'] = [
                        '@attributes' => [
                            'value' => $this->parseDate($office_visit_date['to'])
                        ]
                    ];
                }

                $entry = [
                    '@attributes' => [
                        'typeCode' => 'DRIV'
                    ],
                    'encounter' => [
                        '@attributes' => [
                            'classCode' => 'ENC',
                            'moodCode' => 'EVN'
                        ],
                        'templateId' => [
                            [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.22.4.49'
                                ]
                            ],
                            [
                                '@attributes' => [
                                    'root' => '2.16.840.1.113883.10.20.24.3.23'
                                ]
                            ]
                        ],
                        'id' => [
                            '@attributes' => [
                                'root' => UUID::v4()
                            ]
                        ],
                        'code' => [
                            '@attributes' => [
                                'code' => '99213',
                                'codeSystem' => $this->codes('CPT4'),
                                'displayName' => 'CPT-4'
                            ]
                        ],
                        'statusCode' => [
                            '@attributes' => [
                                'code' => 'completed'
                            ]
                        ]
                    ]
                ];

	            $entry['encounter']['effectiveTime'] = $serviceDate;

	            $entry['encounter']['performer'] = [
		            'assignedEntity' => [
			            '@attributes' => [
				            'classCode' => 'ASSIGNED'
			            ],
			            'id' => [
				            '@attributes' => [
					            'root' => UUID::v4()
				            ]
			            ],
			            'code' => [
				            '@attributes' => [
					            'code' => '59058001',
					            'codeSystem' => '2.16.840.1.113883.6.96',
					            'codeSystemName' => 'SNOMED-CT',
					            'displayName' => $providerInfo['fname'].' '.$providerInfo['mname'].' '.$providerInfo['lname']
				            ]
			            ]
		            ]
	            ];

	            /**
	             * Encounter Diagnosis
	             */
	            if(!empty($encounter_dxs)){

		            $entryRelationshipDiagnosis = [];
		            foreach ($encounter_dxs as $encounter_dx){

			            $dx_date = $serviceDate;

		            	if(isset($encounter_dx['status']) && $encounter_dx['status'] != ''){

		            		if(isset($encounter_dxs['resolved_date']) && $encounter_dxs['resolved_date'] != '0000-00-00 00:00:00'){
					            $dx_date['high'] = [
						            '@attributes' => [
							            'value' => $this->parseDate($encounter_dxs['resolved_date'])
						            ]
					            ];
				            }else{
					            $dx_date['high'] = [
						            '@attributes' => [
							            'nullFlavor' => 'NI'
						            ]
					            ];
				            }
		            		$status = [
					            '@attributes' => [
						            'xsi:type' => 'CD',
						            'code' => $encounter_dx['status_code'],
						            'codeSystem' => $this->codes($encounter_dx['status_code_type']),
						            'codeSystemName' => $encounter_dx['status_code_type'],
						            'displayName' => $encounter_dx['status']
					            ]
				            ];
			            }else{
				            $dx_date['high'] = [
					            '@attributes' => [
						            'nullFlavor' => 'NI'
					            ]
				            ];
				            $status = false;
			            }

			            $entryRelationshipDiagnosisBuff = [
				            '@attributes' => [
					            'typeCode' => 'SUBJ'
				            ],
				            'observation' => [
					            '@attributes' => [
						            'classCode' => 'OBS',
						            'moodCode' => 'EVN',
						            'negationInd' => 'true'
					            ],
					            'templateId' => [
						            '@attributes' => [
							            'root' => '2.16.840.1.113883.10.20.22.4.4'
						            ]
					            ],
					            'id' => [
						            '@attributes' => [
							            'root' => UUID::v4()
						            ]
					            ],
					            'code' => [
						            '@attributes' => [
							            'code' => '282291009',
							            'codeSystem' => '2.16.840.1.113883.6.96',
							            'codeSystemName' => 'SNOMED-CT',
							            'displayName' => 'Diagnosis'
						            ]
					            ],
					            'statusCode' => [
						            '@attributes' => [
							            'code' => 'completed'
						            ]
					            ],
					            'effectiveTime' => $dx_date,
					            'value' => [
						            '@attributes' => [
							            'xsi:type' => 'CD',
							            'code' => $encounter_dx['code'],
							            'codeSystem' => $this->codes($encounter_dx['code_type']),
							            'codeSystemName' => $encounter_dx['code_type'],
							            'displayName' => $encounter_dx['code_text']
						            ]
					            ]
				            ]
			            ];


		            	if($status !== false){
				            $entryRelationshipDiagnosisBuff['observation']['entryRelationship'] = [
					            '@attributes' => [
						            'typeCode' => 'REFR'
					            ],
					            'observation' => [
						            '@attributes' => [
							            'classCode' => 'OBS',
							            'moodCode' => 'EVN',
							            'negationInd' => 'true'
						            ],
						            'templateId' => [
							            '@attributes' => [
								            'root' => '2.16.840.1.113883.10.20.22.4.6'
							            ]
						            ],
						            'id' => [
							            '@attributes' => [
								            'root' => UUID::v4()
							            ]
						            ],
						            'code' => [
							            '@attributes' => [
								            'code' => '33999-4',
								            'codeSystem' => '2.16.840.1.113883.6.1',
								            'codeSystemName' => 'LOINC',
								            'displayName' => 'Status'
							            ]
						            ],
						            'statusCode' => [
							            '@attributes' => [
								            'code' => 'completed'
							            ]
						            ],
						            'effectiveTime' => $dx_date,
						            'value' => $status
					            ]
				            ];
			            }

			            $entryRelationshipDiagnosis[] = $entryRelationshipDiagnosisBuff;
		            }

		            $entry['encounter']['entryRelationship'] = [
			            '@attributes' => [
				            'typeCode' => 'SUBJ'
			            ],
			            'act' => [
				            '@attributes' => [
					            'classCode' => 'ACT',
					            'moodCode'=> 'EVN'
				            ],
				            'templateId' => [
					            '@attributes' => [
						            'root' => '2.16.840.1.113883.10.20.22.4.80'
					            ]
				            ],
				            'id' =>[
					            '@attributes' => [
						            'root' => UUID::v4()
					            ]
				            ],
				            'code' => [
					            '@attributes' => [
						            'code' => '29308-4',
						            'codeSystem' => '2.16.840.1.113883.6.1',
						            'codeSystemName' => 'LOINC',
						            'displayName' => 'Encounter Diagnosis'
					            ]
				            ],
				            'statusCode' => [
					            '@attributes' => [
						            'code' => 'completed'
					            ]
				            ],
				            'entryRelationship' => $entryRelationshipDiagnosis
			            ]
		            ];
	            }

                $encounters['entry'][] = $entry;
            }
        }

        if($this->requiredEncounters || !empty($encounters['entry'])){
            $this->addSection(['section' => $encounters]);
        }
        unset($encountersData, $encounters);
    }
}

/**
 * Handle the request only if pid and action is available
 */
if(isset($_REQUEST['pid']) && isset($_REQUEST['action'])){
    try {
        // Check token for security
        include_once(ROOT . '/sites/' . $_REQUEST['site'] . '/conf.php');
        include_once(ROOT . '/classes/MatchaHelper.php');
        $ccd = new CCDDocument();

        if(isset($_REQUEST['eid'])) $ccd->setEid($_REQUEST['eid']);
        if(isset($_REQUEST['pid'])) $ccd->setPid($_REQUEST['pid']);
        if(isset($_REQUEST['exclude'])) $ccd->setExcludes($_REQUEST['exclude']);

        $ccd->setTemplate('toc');
        $ccd->createCCD();

        switch($_REQUEST['action']){
            case 'view':
                // View the CDA
                $ccd->view();
                break;
            case 'export':
                // Export the CDA
                $ccd->export();
                break;
            case 'archive':
                // Archive the CDA
                $ccd->archive();
                break;
        }
        unset($logObject);
    } catch(Exception $Error) {
        error_log($Error->getMessage());
    }
}
