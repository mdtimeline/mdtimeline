<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
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
include_once(ROOT . '/classes/MatchaHelper.php');
include_once(ROOT . '/dataProvider/Patient.php');
include_once(ROOT . '/lib/HL7/HL7.php');
include_once(ROOT . '/lib/HL7/HL7Client.php');

class HL7Messages {
	/**
	 * @var PDO
	 */
	public $conn;
	/**
	 * @var HL7
	 */
	public $hl7;
	/**
	 * @var MatchaCUP HL7Messages
	 */
	private $m;
	/**
	 * @var MatchaCUP Facility
	 */
	private $f;
	/**
	 * @var MatchaCUP HL7Client
	 */
	private $c;

	/**
	 * @var bool|MatchaCUP Patient Contacts
	 */
	private $PatientContacts;

	/**
	 * @var MatchaCUP Encounter Services
	 */
	private $EncounterServices;

	/**
	 * Lists
	 * @var
	 */
	private $ListOptions;

	/**
	 * @var MatchaCUP PatientImmunization
	 */
	private $i;
	/**
	 * @var MatchaCUP Patient
	 */
	private $p;
	/**
	 * @var MatchaCUP Encounter
	 */
	private $e;
	/**
	 * @var MatchaCUP User
	 */
	private $dx;
	/**
	 * @var MatchaCUP User
	 */
	private $u;
	/**
	 * @var MatchaCUP Referring Provider/Physician
	 */
	private $ReferringProvider;
	/**
	 * @var stdClass
	 */
	private $msg;
	/**
	 * @var int|array
	 */
	private $to;
	/**
	 * @var int|array
	 */
	private $from;
	/**
	 * @var int|object
	 */
	private $patient;
	/**
	 * @var bool|int|stdClass
	 */
	private $encounter;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var stdClass
	 */
	private $map_codes_types;
	/**
	 * @var
	 */
	private $fid;
	/**
	 * @var string
	 */
	private $namespace_id = 'MDTL001';
	/**
	 * @var bool
	 */
	private $anonymous = false;

	function __construct() {
		$this->hl7 = new HL7();
		$this->conn = Matcha::getConn();
		$this->p = MatchaModel::setSenchaModel('App.model.patient.Patient');
		$this->PatientContacts = MatchaModel::setSenchaModel('App.model.patient.PatientContacts');
		$this->EncounterServices = MatchaModel::setSenchaModel('App.model.patient.EncounterService');
		$this->e = MatchaModel::setSenchaModel('App.model.patient.Encounter');
		$this->dx = MatchaModel::setSenchaModel('App.model.patient.EncounterDx');
		$this->u = MatchaModel::setSenchaModel('App.model.administration.User');
		$this->ReferringProvider = MatchaModel::setSenchaModel('App.model.administration.ReferringProvider');
		$this->m = MatchaModel::setSenchaModel('App.model.administration.HL7Message');
		$this->c = MatchaModel::setSenchaModel('App.model.administration.HL7Client');
		$this->f = MatchaModel::setSenchaModel('App.model.administration.Facility');
		$this->d = MatchaModel::setSenchaModel('App.model.administration.EducationResource');
		$this->ListOptions = MatchaModel::setSenchaModel('App.model.administration.ListOptions');
	}

	function broadcastADT($params) {
		$this->c->addFilter('active', 1);
		$clients = $this->c->load()->all();

		if(isset($params->map_codes_types)){
			$this->map_codes_types = $params->map_codes_types;
		}

		if(isset($params->anonymous)){
			$this->anonymous = $params->anonymous;
		}

		foreach($clients as $client){
			$foo = new stdClass();
			$foo->to = $client['id'];
			$foo->from = $params->fid;
			$foo->pid = $params->pid;
			$foo->eid = isset($params->eid) ? $params->eid : 0;
			$this->sendADT($foo, $params->event);
			unset($foo);
		}
		return ['success' => true];
	}

	/**
	 * @param $params
	 * @param $event
	 * @throws Exception
	 */
	function sendADT($params, $event) {

		$this->to = $params->to;
		$this->from = $params->from;
		$this->patient = $params->pid;
		$this->encounter = isset($params->eid) ? $params->eid : 0;
		$this->type = 'ADT';

		if(isset($params->map_codes_types)){
			$this->map_codes_types = $params->map_codes_types;
		}

		if(isset($params->anonymous)){
			$this->anonymous = $params->anonymous;
		}


		// MSH
		$msh = $this->setMSH(true);
		$msh->setValue('9.1', 'ADT');
		$msh->setValue('9.2', $event);
		$msh->setValue('9.3', 'ADT_A01');

		$msh->setValue('21.1', 'PH_SS-NoAck');
		$msh->setValue('21.2', 'SS Sender');
		$msh->setValue('21.3', '2.16.840.1.114222.4.10.3');
		$msh->setValue('21.4', 'ISO');

		$this->setEVN();

		$this->setPID();

		$this->setPV1();

		// Continue with message
		if($event == 'A04'){

			// Specialty
			$obx = $this->hl7->addSegment('OBX');
			$obx->setValue('1', 1);
			$obx->setValue('2', 'CWE');
			$obx->setValue('3.1', 'SS003');
			$obx->setValue('3.3', 'PHINQUESTION');

			$sth = $this->conn->prepare('SELECT * FROM `specialties` WHERE id = ?');
			$sth->execute([$this->encounter->specialty_id]);
			$specialty = $sth->fetch(PDO::FETCH_ASSOC);
			if($specialty !== false){
				$obx->setValue('5.1', $specialty['taxonomy']);
				$obx->setValue('5.2', $specialty['title']);
				$obx->setValue('5.3', 'NUCC');
				$obx->setValue('11', 'F');
			}
			unset($obx);

			// Age - Reportedx
			$obx = $this->hl7->addSegment('OBX');
			$obx->setValue('1', 2);
			$obx->setValue('2', 'NM');
			$obx->setValue('3.1', '21612-7');
			$obx->setValue('3.3', 'LN');
			$obx->setValue('5', (string)$this->patient->age['DMY']['years']);
			$obx->setValue('6.1', 'a');
			$obx->setValue('6.3', 'UCUM');
			$obx->setValue('11', 'F');
			unset($obx);

			$obx = $this->hl7->addSegment('OBX');
			$obx->setValue('1', 3);
			$obx->setValue('2', 'CWE');
			$obx->setValue('3.1', '8661-1');
			$obx->setValue('3.3', 'LN');
			$obx->setValue('5.9', $this->encounter->brief_description);
			$obx->setValue('11', 'F');

			// get diagnosis...
			$diagnoses = $this->dx->load(['eid' => $this->encounter->eid])->all();
			$index = 1;
			foreach($diagnoses as $diagnosis){
				$dg1 = $this->hl7->addSegment('DG1');
				$dg1->setValue('1', $index);
				$dg1->setValue('3.1', $diagnosis['code']);
				$dg1->setValue('3.2', $diagnosis['code_text']);
				$dg1->setValue('3.3', $this->cleanCodeType($diagnosis['code_type']));
				$dg1->setValue('6', $diagnosis['dx_type']);
				$index++;
			}
			unset($index);
		}

		$msgRecord = $this->saveMsg();

		if($this->to['route'] == 'file'){
			$response = $this->Save();
		} else {
			$response = $this->Send();
		}

		if($response['success']){
			$msgRecord->status = 3;
			$this->m->save($msgRecord);
		} else {
			$msgRecord->status = preg_match('/^socket/', $response['message']) ? 2 : 4; // if socket error put back in queue
			$msgRecord->error = $response['message'];
			$this->m->save($msgRecord);
		}

	}

	/**
	 * @param $to
	 * @param $from
	 * @param stdClass $service
	 * @param $orderControl
	 * @return array
	 * @throws Exception
	 */
	function sendServiceORM($to, $from, $service, $orderControl) {
		try {
			$service = (object) $service;
			$this->to = $to;
			$this->from = $from;
			$this->patient = $service->pid;
			$this->encounter = $service->eid;
			$this->type = 'ORM';

			if(isset($service->map_codes_types)){
				$this->map_codes_types = $service->map_codes_types;
			}

			// MSH
			$msh = $this->setMSH();
			$msh->setValue('9.1', 'ORM');
			$msh->setValue('9.2', 'O01');

			// PID
			$this->setPID();
			// PV1
			$this->setPV1();
			// ORC
			$this->setORC($service, $orderControl);
			// OBR
			$this->setOBR($service, 1);

			if(is_array($service->dx_pointers)){
				$dxIndex = 1;
				foreach($service->dx_pointers as $dx){
					$this->setDG1($dx, $dxIndex);
					$dxIndex++;
				}
			}

			$msgRecord = $this->saveMsg();

			if($this->to['route'] == 'file'){
				$response = $this->Save();
			} else {
				$response = $this->Send();
			}

			$msgRecord->response = $response['message'];

			if($response['success']){
				$msgRecord->status = 3;
				$this->m->save($msgRecord);
			} else {
				$msgRecord->status = preg_match('/^socket/', $response['message']) ? 2 : 4; // if socket error put back in queue
				$msgRecord->error = $response['message'];
				$this->m->save($msgRecord);
			}
			return ['success' => true];
		} catch(Exception $Error) {
			return ['success' => false];
		}
	}

	function sendVXU($params) {
		try {
			// set these globally to be used by MSH and PID
			$this->to = $params->to;
			$this->from = $params->from;
			$this->patient = $params->pid;
			$this->encounter = isset($params->eid) ? $params->eid : 0;
			$this->type = 'VXU';

			if(isset($params->map_codes_types)){
				$this->map_codes_types = $params->map_codes_types;
			}

			// MSH
			$msh = $this->setMSH();
			$msh->setValue('9.1', 'VXU');
			$msh->setValue('9.2', 'V04');
			$msh->setValue('9.3', 'VXU_V04');
			// PID
			$this->setPID();
			// PV1
			$this->setPV1();

			$this->setPD1();

			$this->setNK1s();

			$this->i = MatchaModel::setSenchaModel('App.model.patient.PatientImmunization');
			include_once(ROOT . '/dataProvider/Immunizations.php');
			include_once(ROOT . '/dataProvider/Services.php');
			$immunization = new Immunizations();
			$EncounterServices = new Services();

			// Immunizations loop
			foreach($params->immunizations AS $i){

				$immu = $this->i->load($i)->one();
				$obx_group_sub_id = 1;

				// ORC - 4.5.1 ORC - Common Order Segment
				$ORC = $this->hl7->addSegment('ORC');
				$ORC->setValue('1', 'RE'); //HL70119
				$ORC->setValue('3.1', 'GAIA10001');
				$ORC->setValue('3.2', $immu['id']);

				if($this->notEmpty($immu['administered_uid'])){
					$this->u->clearFilters();
					$this->u->addFilter('id', $immu['administered_uid']);
					$administered_by = $this->u->load()->one();
					if($administered_by !== false){
						$ORC->setValue('10.1', $administered_by['id']);
						$ORC->setValue('10.2.1', $administered_by['lname']);
						$ORC->setValue('10.3', $administered_by['fname']);
						$ORC->setValue('10.4', $administered_by['mname']);
						$ORC->setValue('10.9.1', $this->namespace_id);
						$ORC->setValue('10.10', 'L');
					}
				}

				if($this->notEmpty($immu['created_uid'])){
					$this->u->clearFilters();
					$this->u->addFilter('id', $immu['created_uid']);
					$ordered_by = $this->u->load()->one();
					if($ordered_by !== false){
						$ORC->setValue('12.1', $ordered_by['id']);
						$ORC->setValue('12.2.1', $ordered_by['lname']);
						$ORC->setValue('12.3', $ordered_by['fname']);
						$ORC->setValue('12.4', $ordered_by['mname']);
						$ORC->setValue('12.9.1', $this->namespace_id);
						$ORC->setValue('12.10', 'L');
					}
				}


				// RXA - 4.14.7 RXA - Pharmacy/Treatment Administration Segment
				$RXA = $this->hl7->addSegment('RXA');
				$RXA->setValue('3.1', $this->date($immu['administered_date'])); //Date/Time Start of Administration
				$RXA->setValue('4.1', $this->date($immu['administered_date'])); //Date/Time End of Administration
				//Administered Code
				$RXA->setValue('5.1', $immu['code']); //Identifier
				$RXA->setValue('5.2', $immu['vaccine_name']); //Text
				$RXA->setValue('5.3', $immu['code_type']); //Name of Coding System

				if($this->isPresent($immu['administer_amount'])){
					$RXA->setValue('6', $immu['administer_amount']); //Administered Amount
					$RXA->setValue('7.1', $immu['administer_units']); //Identifier
					$RXA->setValue('7.2', $immu['administer_units']); // Text
					$RXA->setValue('7.3', 'UCUM'); //Name of Coding System HL70396
					$administered = true;
				} else {
					$RXA->setValue('6', '999'); //Administered Amount
					$administered = false;
				}

				if($this->notEmpty($immu['information_source_code'])){
					$this->ListOptions->clearFilters();
					$this->ListOptions->addFilter('list_id', 138);
					$this->ListOptions->addFilter('option_value', $immu['information_source_code']);
					$Record = $this->ListOptions->load()->one();
					$RXA->setValue('9.1', $Record['option_value']);
					$RXA->setValue('9.2', $Record['option_name']);
					$RXA->setValue('9.3', 'NIP001');
				}

				if($this->notEmpty($immu['administered_uid'])){
					if(isset($administered_by) && $administered_by !== false){
						$RXA->setValue('10.1', $administered_by['id']);
						$RXA->setValue('10.2.1', $administered_by['lname']);
						$RXA->setValue('10.3', $administered_by['fname']);
						$RXA->setValue('10.4', $administered_by['mname']);
						$RXA->setValue('10.9.1', $this->namespace_id);
						$RXA->setValue('10.10', 'L');
					}
				}

				if($this->notEmpty($immu['facility_id']) && $administered){
					$RXA->setValue('11.4.1', $immu['facility_id']);
				}

				if($this->notEmpty($immu['exp_date'])){
					$RXA->setValue('16.1', $this->date($immu['exp_date']));
				}

				$RXA->setValue('15', $immu['lot_number']);

				if($this->notEmpty($immu['manufacturer'])){
					// get immunization manufacturer info
					$mvx = $immunization->getMvxByCode($immu['manufacturer']);
					$mText = isset($mvx['manufacturer']) ? $mvx['manufacturer'] : '';
					//Substance ManufacturerName
					$RXA->setValue('17.1', $immu['manufacturer']); //Identifier
					$RXA->setValue('17.2', $mText); //Text
					$RXA->setValue('17.3', 'MVX'); //Name of Coding System HL70396
				}

				if($this->notEmpty($immu['refusal_reason_code'])){
					$this->ListOptions->clearFilters();
					$this->ListOptions->addFilter('list_id', 139);
					$this->ListOptions->addFilter('option_value', $immu['refusal_reason_code']);
					$Record = $this->ListOptions->load()->one();
					if($Record !== false){
						$RXA->setValue('18.1', $Record['option_value']);
						$RXA->setValue('18.2', $Record['option_name']);
						$RXA->setValue('18.3', 'NIP002');
						$RXA->setValue('20', 'RE');
					}
				}else if($immu['code'] == '998'){
					$RXA->setValue('20', 'NA');
				}else{
					$RXA->setValue('20', 'CP'); //complete
				}

				$RXA->setValue('21', 'A'); //Action Code

				// RXR - 4.14.2 RXR - Pharmacy/Treatment Route Segment
				$RXR = $this->hl7->addSegment('RXR');
				// Route

				$this->ListOptions->clearFilters();
				$this->ListOptions->addFilter('list_id', 6);
				$this->ListOptions->addFilter('option_value', $immu['route']);
				$Record = $this->ListOptions->load()->one();
				$RXR->setValue('1.1', $Record['option_value']);
				$RXR->setValue('1.2', $Record['option_name']);
				$RXR->setValue('1.3', $Record['code_type']);
				// Administration Site
				$this->ListOptions->clearFilters();
				$this->ListOptions->addFilter('list_id', 119);
				$this->ListOptions->addFilter('code', $immu['administration_site']);
				$Record = $this->ListOptions->load()->one();
				$RXR->setValue('2.1', $Record['option_value']);
				$RXR->setValue('2.2', $Record['option_name']);
				$RXR->setValue('2.3', $Record['code_type']);

				// OBX - 7.4.2 OBX - Observation/Result Segment
				$this->ListOptions->clearFilters();
				$this->ListOptions->addFilter('list_id', 135);
				$this->ListOptions->addFilter('option_value', $immu['vfc_code']);
				$Record = $this->ListOptions->load()->one();

				$obxCount = 1;

				if($Record !== false){
					$OBX = $this->hl7->addSegment('OBX');
					$OBX->setValue('1', $obxCount);
					$OBX->setValue('2', 'CE');
					$OBX->setValue('3.1', '64994-7');
					$OBX->setValue('3.2', 'Vaccine funding program eligibility category');
					$OBX->setValue('3.3', 'LN');
					$OBX->setValue('4', isset($immu['eid']) && $immu['eid'] > 0 ? $immu['eid'] : '0');
					$OBX->setValue('5.1', $Record['option_value']);
					$OBX->setValue('5.2', $Record['option_name']);
					$OBX->setValue('5.3', $Record['code_type']);
					$OBX->setValue('11', 'F');
					$OBX->setValue('17.1', 'VXC40');
					$OBX->setValue('17.2', 'Eligibility captured at the immunization level');
					$OBX->setValue('17.3', 'CDCPHINVS');
					$obxCount++;
				}

				if($this->notEmpty($immu['education_resource_1_id']) && $immu['education_resource_1_id'] > 0){
					$document = $this->d->load(['id' => $immu['education_resource_1_id']])->one();
					if($document !==  false){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'CE');
						$OBX->setValue('3.1', '30956-7');
						$OBX->setValue('3.2', 'vaccine type');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_1_id']);
						$OBX->setValue('5.1', $document['code']);
						$OBX->setValue('5.2', $document['code_text']);
						$OBX->setValue('5.3', $document['code_type']);
						$OBX->setValue('11', 'F');
						$obxCount++;

						if($this->notEmpty($document['publication_date'])){
							$OBX = $this->hl7->addSegment('OBX');
							$OBX->setValue('1', $obxCount);
							$OBX->setValue('2', 'TS');
							$OBX->setValue('3.1', '29768-9');
							$OBX->setValue('3.2', 'Date vaccine information statement published');
							$OBX->setValue('3.3', 'LN');
							$OBX->setValue('4', $immu['education_resource_1_id']);
							$OBX->setValue('5', $this->date($document['publication_date'], false));
							$OBX->setValue('11', 'F');
							$obxCount++;
						}
					}

					if($this->notEmpty($immu['education_presented_1_date'])){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'TS');
						$OBX->setValue('3.1', '29769-7');
						$OBX->setValue('3.2', 'Date vaccine information statement presented');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_1_id']);
						$OBX->setValue('5', $this->date($immu['education_presented_1_date'], false));
						$OBX->setValue('11', 'F');
						$obxCount++;
					}
				}

				if($this->notEmpty($immu['education_resource_2_id']) && $immu['education_resource_2_id'] > 0){
					$document = $this->d->load(['id' => $immu['education_resource_2_id']])->one();
					if($document !==  false){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'CE');
						$OBX->setValue('3.1', '30956-7');
						$OBX->setValue('3.2', 'vaccine type');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_2_id']);
						$OBX->setValue('5.1', $document['code']);
						$OBX->setValue('5.2', $document['code_text']);
						$OBX->setValue('5.3', $document['code_type']);
						$OBX->setValue('11', 'F');
						$obxCount++;

						if($this->notEmpty($document['publication_date'])){
							$OBX = $this->hl7->addSegment('OBX');
							$OBX->setValue('1', $obxCount);
							$OBX->setValue('2', 'TS');
							$OBX->setValue('3.1', '29768-9');
							$OBX->setValue('3.2', 'Date vaccine information statement published');
							$OBX->setValue('3.3', 'LN');
							$OBX->setValue('4', $immu['education_resource_2_id']);
							$OBX->setValue('5', $this->date($document['publication_date'], false));
							$OBX->setValue('11', 'F');
							$obxCount++;
						}
					}

					if($this->notEmpty($immu['education_presented_2_date'])){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'TS');
						$OBX->setValue('3.1', '29769-7');
						$OBX->setValue('3.2', 'Date vaccine information statement presented');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_2_id']);
						$OBX->setValue('5', $this->date($immu['education_presented_2_date'], false));
						$OBX->setValue('11', 'F');
						$obxCount++;
					}
				}


				if($this->notEmpty($immu['education_resource_3_id']) && $immu['education_resource_3_id'] > 0){
					$document = $this->d->load(['id' => $immu['education_resource_3_id']])->one();
					if($document !==  false){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'CE');
						$OBX->setValue('3.1', '30956-7');
						$OBX->setValue('3.2', 'vaccine type');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_3_id']);
						$OBX->setValue('5.1', $document['code']);
						$OBX->setValue('5.2', $document['code_text']);
						$OBX->setValue('5.3', $document['code_type']);
						$OBX->setValue('11', 'F');
						$obxCount++;

						if($this->notEmpty($document['publication_date'])){
							$OBX = $this->hl7->addSegment('OBX');
							$OBX->setValue('1', $obxCount);
							$OBX->setValue('2', 'TS');
							$OBX->setValue('3.1', '29768-9');
							$OBX->setValue('3.2', 'Date vaccine information statement published');
							$OBX->setValue('3.3', 'LN');
							$OBX->setValue('4', $immu['education_resource_3_id']);
							$OBX->setValue('5', $this->date($document['publication_date'], false));
							$OBX->setValue('11', 'F');
							$obxCount++;
						}
					}

					if($this->notEmpty($immu['education_presented_3_date'])){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'TS');
						$OBX->setValue('3.1', '29769-7');
						$OBX->setValue('3.2', 'Date vaccine information statement presented');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['education_resource_3_id']);
						$OBX->setValue('5', $this->date($immu['education_presented_3_date'], false));
						$OBX->setValue('11', 'F');
						$obxCount++;
					}
				}



				if($this->notEmpty($immu['is_presumed_immunity']) && $this->notEmpty($immu['presumed_immunity_code'])){

					$this->ListOptions->clearFilters();
					$this->ListOptions->addFilter('list_id', 140);
					$this->ListOptions->addFilter('option_value', $immu['presumed_immunity_code']);
					$Record = $this->ListOptions->load()->one();

					if($Record !== false){
						$OBX = $this->hl7->addSegment('OBX');
						$OBX->setValue('1', $obxCount);
						$OBX->setValue('2', 'CE');
						$OBX->setValue('3.1', '59784-9');
						$OBX->setValue('3.2', 'Disease with presumed immunity');
						$OBX->setValue('3.3', 'LN');
						$OBX->setValue('4', $immu['id']);
						$OBX->setValue('5.1', $Record['option_value']);
						$OBX->setValue('5.2', $Record['option_name']);
						$OBX->setValue('5.3', $Record['code_type']);
						$OBX->setValue('11', 'F');
						$obxCount++;
					}
				}
			}

			$msgRecord = $this->saveMsg();

			// If the delivery is set and for download, quit the rest of the process
			if(isset($params->delivery) && $params->delivery = 'download')
				return;

			if($this->to['route'] == 'file'){
				$response = $this->Save();
			} else {
				$response = $this->Send();
			}

			$msgRecord->response = $response['message'];

			if($response['success']){
				$msgRecord->status = 3;
				$this->m->save($msgRecord);
			} else {
				$msgRecord->status = preg_match('/^socket/', $response['message']) ? 2 : 4; // if socket error put back in queue
				$msgRecord->error = $response['message'];
				$this->m->save($msgRecord);
			}
			return ['success' => true];
		} catch(Exception $Error) {
			return ['success' => false];
		}
	}

	private function setMSH($includeNPI = false) {
		$this->setEncounter();

		$this->fid = $this->from;

		// set these globally
		$this->to = $this->c->load($this->to)->one();
		$this->from = $this->f->load($this->from)->one();
		//
		$msh = $this->hl7->addSegment('MSH');
		$msh->setValue('3.1', 'GaiaEHR'); // Sending Application
		$msh->setValue('4.1', addslashes(substr($this->from['name'], 0, 20))); // Sending Facility
		if($includeNPI){
			$msh->setValue('4.2', $this->from['npi']);
			$msh->setValue('4.3', 'NPI');
		}

		$msh->setValue('5.1', $this->to['application_name']); // Receiving Application
		$msh->setValue('6.1', $this->to['facility']); // Receiving Facility
		$msh->setValue('7.1', date('YmdHis')); // Message Date Time
		$msh->setValue('11.1', 'P'); // D = Debugging P = Production T = Training
		$msh->setValue('12.1', '2.5.1'); // HL7 version
		$msh->setValue('15', 'AL');
		$msh->setValue('16', 'ER');
		return $msh;
	}

	private function setEVN() {
		$evn = $this->hl7->addSegment('EVN');
		$evn->setValue('2.1', date('YmdHis'));
		$evn->setValue('7.1', str_replace(' ', '', substr($this->from['name'], 0, 20)));
		$evn->setValue('7.2', $this->from['npi']);
		$evn->setValue('7.3', 'NPI');
	}

	private function setPID() {

		$this->patient = $this->p->load($this->patient)->one();

		if($this->patient == false){
			throw new \Exception('Error: Patient not found during setPID, Record # ' . $this->patient);
		}

		$this->patient = (object)$this->patient;

		$this->patient->age = Patient::getPatientAgeByDOB($this->patient->DOB);

		$pid = $this->hl7->addSegment('PID');

		$pid->setValue('1', 1);

		$index = 0;
		if($this->notEmpty($this->patient->pubpid)){
			$pid->setValue('3.1', $this->patient->pubpid, $index);
			$pid->setValue('3.4', $this->namespace_id);
			$pid->setValue('3.5', 'MR', $index); // IDNumber Type (HL70203) MR = Medical Record
			$index++;
		} elseif($this->notEmpty($this->patient->pid)) {
			$pid->setValue('3.1', $this->patient->pid, $index);
			$pid->setValue('3.4', $this->namespace_id);
			$pid->setValue('3.5', 'MR', $index);  // IDNumber Type (HL70203) MR = Medical Record
			$index++;
		}
		// added SS if exist
		if($this->notEmpty($this->patient->SS)){
			$pid->setValue('3.1', $this->patient->SS, $index);
			$pid->setValue('3.4', 'SSA', $index);
			$pid->setValue('3.5', 'SS', $index); // IDNumber Type (HL70203) SS = Social Security
		}
		unset($index);

		if($this->anonymous){
			$pid->setValue('5.7', 'S', 1);
		} else {
			if($this->notEmpty($this->patient->lname)){
				$pid->setValue('5.1.1', $this->patient->lname);
			}
			if($this->notEmpty($this->patient->fname)){
				$pid->setValue('5.2', $this->patient->fname);
			}
			if($this->notEmpty($this->patient->mname)){
				$pid->setValue('5.3', $this->patient->mname);
			}
			$pid->setValue('5.7', 'L');
		}

		if($this->notEmpty($this->patient->mother_lname)){
			$pid->setValue('6.1.1', $this->patient->mother_lname);
		}
		if($this->notEmpty($this->patient->mother_fname)){
			$pid->setValue('6.2', $this->patient->mother_fname);
		}
		if($this->notEmpty($this->patient->mother_mname)){
			$pid->setValue('6.3', $this->patient->mother_mname);
		}
		if($this->notEmpty($this->patient->DOB)){
			$pid->setValue('7.1', $this->date($this->patient->DOB));
		}
		if($this->notEmpty($this->patient->sex)){
			$pid->setValue('8', $this->patient->sex);
		}
		if($this->notEmpty($this->patient->alias)){
			$pid->setValue('9.2', $this->patient->alias);
		}
		if($this->notEmpty($this->patient->race)){
			$pid->setValue('10.1', $this->patient->race);
			$pid->setValue('10.2', $this->hl7->race($this->patient->race)); //Race Text
			$pid->setValue('10.3', 'CDCREC'); // Race Name of Coding System
		}

		$has_address = false;
		if($this->notEmpty($this->patient->physical_address)){
			$pid->setValue('11.1.1', $this->patient->physical_address . ' ' . $this->patient->physical_address_cont);
			$has_address = true;
		}

		if($this->notEmpty($this->patient->physical_city)){
			$pid->setValue('11.3', $this->patient->physical_city);
			$has_address = true;
		}

		if($this->notEmpty($this->patient->physical_state)){
			$pid->setValue('11.4', $this->patient->physical_state);
			$has_address = true;
		}
		if($this->notEmpty($this->patient->physical_zip)){
			$pid->setValue('11.5', $this->patient->physical_zip);
			$has_address = true;
		}
		if($this->notEmpty($this->patient->physical_country)){
			$pid->setValue('11.6', $this->patient->physical_country);
			$has_address = true;
		}

		if($has_address){
			$pid->setValue('11.7', 'L'); // Address Type L = Legal Address
			$pid->setValue('11.9', '25025');
		}

		$index = 0;
		if($this->notEmpty($this->patient->phone_home)){
			$phone = $this->phone($this->patient->phone_home);
			$pid->setValue('13.2', 'PRN', $index);              // PhoneNumber‐Home
			$pid->setValue('13.3', 'PH', $index);               // PhoneNumber‐Home
			$pid->setValue('13.6', $phone['area'], $index);     // Area/City Code
			$pid->setValue('13.7', $phone['number'], $index);   // LocalNumber
			$index++;
		}
		if($this->notEmpty($this->patient->email)){
			$pid->setValue('13.2', 'NET', $index);
			$pid->setValue('13.4', $this->patient->email, $index);
		}
		unset($index);

		if($this->notEmpty($this->patient->language)){
			$pid->setValue('15.1', $this->patient->language);
		}

		// Marital Status
		if($this->notEmpty($this->patient->marital_status)){
			$list = new stdClass();
			$list->filter[0] = new stdClass();
			$list->filter[0]->property = 'list_id';
			$list->filter[0]->value = '12';
			$ComboListRecord = $this->ListOptions->load($list)->one();
			$pid->setValue('16.1', $this->patient->marital_status); // EthnicGroup Identifier
			$pid->setValue('16.2', $this->hl7->marital($this->patient->marital_status)); // EthnicGroup Text
			$pid->setValue('16.3', $ComboListRecord['code_type']); // Name of Coding System
		}
		if($this->notEmpty($this->patient->pubaccount)){
			$pid->setValue('18.1', $this->patient->pubaccount);
		}

//		if($this->notEmpty($this->patient->SS)){
//			$pid->setValue('19', $this->patient->SS);
//		}

		// Patient Drivers License Information
		if($this->notEmpty($this->patient->drivers_license)){
			$pid->setValue('20.1', $this->patient->drivers_license);
			if($this->notEmpty($this->patient->drivers_license_state)){
				$pid->setValue('20.2', $this->patient->drivers_license_state);
			}
			if($this->notEmpty($this->patient->drivers_license_exp)){
				$pid->setValue('20.3', $this->date($this->patient->drivers_license_exp));
			}
		}

		// Ethnicity
		if($this->notEmpty($this->patient->ethnicity)){
			$this->ListOptions->clearFilters();
			$this->ListOptions->addFilter('list_id', 59);
			$this->ListOptions->addFilter('option_value', $this->patient->ethnicity);
			$buff = $this->ListOptions->load()->one();
			$ethnicity = $this->mapCode($this->patient->ethnicity, $buff['code_type'], 'ethnicity');
			$pid->setValue('22.1', $ethnicity['code']);
			$pid->setValue('22.2', $buff['option_name']);
			$pid->setValue('22.3', $ethnicity['code_type']);
		}

		if($this->notEmpty($this->patient->birth_place)){
			$pid->setValue('23', $this->patient->birth_place);
		}

		// Birth Multiple
		if($this->notEmpty($this->patient->birth_multiple)){
			if($this->patient->birth_multiple){
				$pid->setValue('24', 'Y');
			} else {
				$pid->setValue('24', 'N');
			}
		}
		if($this->notEmpty($this->patient->birth_order)){
			$pid->setValue('25', $this->patient->birth_order);
		}
		if($this->notEmpty($this->patient->citizenship)){
			$pid->setValue('26.1', $this->patient->citizenship);
		}
		if($this->notEmpty($this->patient->is_veteran)){
			$pid->setValue('27.1', $this->patient->is_veteran);
		}
		if($this->notEmpty($this->patient->death_date) && $this->notEmpty($this->patient->deceased)){
			$pid->setValue('29.1', $this->date($this->patient->death_date));
			$pid->setValue('30', 'Y');
		}
		if($this->notEmpty($this->patient->update_date)){
			$pid->setValue('33.1', $this->date($this->patient->update_date));
		}

		return $pid;
	}

	private function setPV1() {

		if($this->encounter === false)
			return;

		$pv1 = $this->hl7->addSegment('PV1');
		$pv1->setValue('1', 1);
		/**
		 * 0004 B Obstetrics
		 * 0004 C Commercial Account
		 * 0004 E Emergency
		 * 0004 I Inpatient
		 * 0004 N Not Applicable
		 * 0004 O Outpatient
		 * 0004 P Preadmit
		 * 0004 R Recurring patient
		 * 0004 U Unknown
		 */
		if($this->notEmpty($this->encounter->patient_class)){
			$pv1->setValue('2', $this->encounter->patient_class);
		} else {
			$pv1->setValue('2', 'U');
		}
		/**
		 * 0007 A Accident
		 * 0007 C Elective
		 * 0007 E Emergency
		 * 0007 L Labor and Delivery
		 * 0007 N Newborn (Birth in healthcare facility)
		 * 0007 R Routine
		 */
		$repIndex = 0;
		if($this->notEmpty($this->encounter->provider_uid)){
			$provider = $this->u->load($this->encounter->provider_uid)->one();
			if($provider !== false){
				$provider = (object)$provider;
				$pv1->setValue('7.1', $provider->npi, $repIndex); // NPI
				$pv1->setValue('7.2.1', $provider->lname, $repIndex); // Last Name
				$pv1->setValue('7.3', $provider->fname, $repIndex); // First Name
				$pv1->setValue('7.4', $provider->mname, $repIndex); // Middle Name
				//$pv1->setValue('7.5', $provider->suffix, $repIndex); // Suffix Sr. Jr
				$pv1->setValue('7.6', $provider->title, $repIndex); // Prefix Title
				$repIndex++;
			}
		}

		if($this->notEmpty($this->encounter->supervisor_uid)){
			$supervisor = $this->u->load($this->encounter->supervisor_uid)->one();
			if($supervisor !== false){
				$provider = (object)$supervisor;
				$pv1->setValue('7.1', $provider->npi, $repIndex); // NPI
				$pv1->setValue('7.2.1', $provider->lname, $repIndex); // Last Name
				$pv1->setValue('7.3', $provider->fname, $repIndex); // First Name
				$pv1->setValue('7.4', $provider->mname, $repIndex); // Middle Name
				$pv1->setValue('7.6', $provider->title, $repIndex); // Prefix Title
				$repIndex++;
			}
		}

		if($this->notEmpty($this->encounter->referring_physician)){
			$referring = $this->ReferringProvider->load($this->encounter->referring_physician)->one();
			if($referring !== false){
				$referring = (object)$referring;
				$pv1->setValue('8.1', $referring->npi); // NPI
				$pv1->setValue('8.2.1', $referring->lname); // Last Name
				$pv1->setValue('8.3', $referring->fname); // First Name
				$pv1->setValue('8.4', $referring->mname); // Middle Name
				$pv1->setValue('8.6', $referring->title); // Prefix Title
			}
		}

		if($this->notEmpty($this->encounter->eid)){
			$pv1->setValue('19.1', $this->encounter->eid);
			$pv1->setValue('19.5', 'VN');
		}

		if($this->notEmpty($this->encounter->service_date)){
			$pv1->setValue('44.1', $this->date($this->encounter->service_date)); // Prefix Title
		}
	}

	private function setPD1() {

		$patient = $this->patient;

		// Variable Objects to pass filter to MatchaCup
		$filters = new stdClass();
		$filters->filter[0] = new stdClass();
		$filters->filter[1] = new stdClass();

		// Load the List option model, to do lookups in the Value Code Sets


		// PD1 - 3.4.10 PD1 - Patient Additional Demographic Segment
		// If the Publicity is set, on the patient contacts compile this HL7 Message line

		$PD1 = $this->hl7->addSegment('PD1');

		if($this->notEmpty($patient->organ_donor_code)){
			$PD1->setValue('8', $patient->organ_donor_code);
		}

		if($this->notEmpty($patient->phone_publicity)){
			$this->ListOptions->clearFilters();
			$this->ListOptions->addFilter('list_id', 132);
			$this->ListOptions->addFilter('code', $patient->phone_publicity);
			$listOptionsRecord = $this->ListOptions->load()->one();
			if($listOptionsRecord !== false){
				$PD1->setValue('11.1', $listOptionsRecord['option_value']);
				$PD1->setValue('11.2', $listOptionsRecord['option_name']);
				$PD1->setValue('11.3', $listOptionsRecord['code_type']);
				$PD1->setValue('18', $this->date($patient->create_date, false));
			}
		}

		if($this->notEmpty($patient->allow_immunization_info_sharing)){
			$PD1->setValue('12', 'N');
			$PD1->setValue('13', $this->date($patient->create_date, false));
		}

		$PD1->setValue('16', 'A');
		$PD1->setValue('17', $this->date($patient->create_date, false));


	}

	private function setNK1s() {

		$patient = $this->patient;

		// Next Kind segment index
		$NK1_index = 1;

		if(isset($patient->guardians_lname) && $patient->guardians_lname != ''){
			$NK1 = $this->hl7->addSegment('NK1');
			$NK1->setValue('1.1', $NK1_index);
			if(isset($patient->guardians_lname) && $patient->guardians_lname != ''){
				$NK1->setValue('2.1.1', $patient->guardians_lname);
			}
			if(isset($patient->guardians_fname) && $patient->guardians_fname != ''){
				$NK1->setValue('2.2', $patient->guardians_fname);
			}
			if(isset($patient->guardians_mname) && $patient->guardians_mname != ''){
				$NK1->setValue('2.3', $patient->guardians_mname);
			}
			$NK1->setValue('2.7', 'L');
			if(isset($patient->guardians_relation) && $patient->guardians_relation != ''){
				$this->ListOptions->clearFilters();
				$this->ListOptions->addFilter('list_id',134);
				$this->ListOptions->addFilter('code',$patient->guardians_relation);
				$listOptionsRecord = $this->ListOptions->load()->one();
				$NK1->setValue('3.1', $listOptionsRecord['option_value']);
				$NK1->setValue('3.2', $listOptionsRecord['option_name']);
				$NK1->setValue('3.3', $listOptionsRecord['code_type']);
			}

			$NK1->setValue('4.1.1', $patient->guardians_address . ' ' . $patient->guardians_address_cont);
			$NK1->setValue('4.3', $patient->guardians_city);
			$NK1->setValue('4.4', $patient->guardians_state);
			$NK1->setValue('4.5', $patient->guardians_zip);
			$NK1->setValue('4.6', $patient->guardians_country);
			$NK1->setValue('4.7', 'L');

			if(isset($patient->guardians_phone) && $patient->guardians_phone != ''){
				$NK1->setValue('5.2', 'PRN');
				$NK1->setValue('5.3', $patient->guardians_phone_type);
				$phone = explode('-', $patient->guardians_phone);
				$NK1->setValue('5.6', $phone[0]);
				$NK1->setValue('5.7', $phone[1] . $phone[2]);
			}

			$NK1_index++;
		}

		if(isset($patient->emergency_contact_lname) && $patient->emergency_contact_lname != ''){
			$NK1 = $this->hl7->addSegment('NK1');
			$NK1->setValue('2.1.1', $NK1_index);
			if(isset($patient->emergency_contact_lname) && $patient->emergency_contact_lname != ''){
				$NK1->setValue('2.1.1', $patient->guardians_lname);
			}
			if(isset($patient->emergency_contact_fname) && $patient->emergency_contact_fname != ''){
				$NK1->setValue('2.2', $patient->emergency_contact_fname);
			}
			if(isset($patient->emergency_contact_mname) && $patient->emergency_contact_mname != ''){
				$NK1->setValue('2.3', $patient->emergency_contact_mname);
			}
			$NK1->setValue('2.7', 'L');
			if(isset($patient->emergency_contact_relation) && $patient->emergency_contact_relation != ''){
				$this->ListOptions->clearFilters();
				$this->ListOptions->addFilter('list_id',134);
				$this->ListOptions->addFilter('code',$patient->emergency_contact_relation);
				$listOptionsRecord = $this->ListOptions->load()->one();
				$NK1->setValue('3.1', $listOptionsRecord['option_value']);
				$NK1->setValue('3.2', $listOptionsRecord['option_name']);
				$NK1->setValue('3.3', $listOptionsRecord['code_type']);
			}

			// TODO Address....
			$NK1->setValue('4.1.2', $patient->emergency_contact_address . ' ' . $patient->emergency_contact_address_cont);
			$NK1->setValue('4.3', $patient->emergency_contact_city);
			$NK1->setValue('4.4', $patient->emergency_contact_state);
			$NK1->setValue('4.5', $patient->emergency_contact_zip);
			$NK1->setValue('4.6', $patient->emergency_contact_country);
			$NK1->setValue('4.7', 'L');

			if(isset($patient->emergency_contact_phone) && $patient->emergency_contact_phone != ''){
				$NK1->setValue('5.2', 'PRN');
				$NK1->setValue('5.3', $patient->emergency_contact_phone_type);
				$phone = explode('-', $patient->emergency_contact_phone);
				$NK1->setValue('5.6', $phone[0]);
				$NK1->setValue('5.7', $phone[1] . $phone[2]);
			}

			$NK1_index++;
		}

	}

	private function setORC($order, $orderControl) {
		if($order === false)
			return;

		$orc = $this->hl7->addSegment('ORC');
		/**
		 * $orderControl shall be one for these values
		 * ----------------------------------------------
		 * 0119 AF Order/service refill request approval
		 * 0119 CA Cancel order/service request
		 * 0119 CH Child order/service
		 * 0119 CN Combined result
		 * 0119 CR Canceled as requested
		 * 0119 DC Discontinue order/service request
		 * 0119 DE Data errors
		 * 0119 DF Order/service refill request denied
		 * 0119 DR Discontinued as requested
		 * 0119 FU Order/service refilled, unsolicited
		 * 0119 HD Hold order request
		 * 0119 HR On hold as requested
		 * 0119 LI Link order/service to patient care problem or goal
		 * 0119 NA Number assigned
		 * 0119 NW New order/service
		 * 0119 OC Order/service canceled
		 * 0119 OD Order/service discontinued
		 * 0119 OE Order/service released
		 * 0119 OF Order/service refilled as requested
		 * 0119 OH Order/service held
		 * 0119 OK Order/service accepted & OK
		 * 0119 OP Notification of order for outside dispense
		 * 0119 OR Released as requested
		 * 0119 PA Parent order/service
		 * 0119 PR Previous Results with new order/service
		 * 0119 PY Notification of replacement order for outside dispense
		 * 0119 RE Observations/Performed Service to follow
		 * 0119 RF Refill order/service request
		 * 0119 RL Release previous hold
		 * 0119 RO Replacement order
		 * 0119 RP Order/service replace request
		 * 0119 RQ Replaced as requested
		 * 0119 RR Request received
		 * 0119 RU Replaced unsolicited
		 * 0119 SC Status changed
		 * 0119 SN Send order/service number
		 * 0119 SR Response to send order/service status request
		 * 0119 SS Send order/service status request
		 * 0119 UA Unable to accept order/service
		 * 0119 UC Unable to cancel
		 * 0119 UD Unable to discontinue
		 * 0119 UF Unable to refill
		 * 0119 UH Unable to put on hold
		 * 0119 UM Unable to replace
		 * 0119 UN Unlink order/service from patient care problem or goal
		 * 0119 UR Unable to release
		 * 0119 UX Unable to change
		 * 0119 XO Change order/service request
		 * 0119 XR Changed as requested
		 * 0119 XX Order/service changed, unsol.
		 */

		$orc->setValue('1', $orderControl);
		$orc->setValue('2.1', $order->id);
		$orc->setValue('9.1', $this->date($this->encounter->service_date));

		$repIndex = 0;
		if($this->notEmpty($this->encounter->provider_uid)){
			$provider = $this->u->load($this->encounter->provider_uid)->one();
			if($provider !== false){
				$provider = (object)$provider;
				$orc->setValue('12.1', $provider->npi, $repIndex); // NPI
				$orc->setValue('12.2.1', $provider->lname, $repIndex); // Last Name
				$orc->setValue('12.3', $provider->fname, $repIndex); // First Name
				$orc->setValue('12.4', $provider->mname, $repIndex); // Middle Name
				//$orc->setValue('7.5', $provider->suffix, $repIndex); // Suffix Sr. Jr
				$orc->setValue('12.6', $provider->title, $repIndex); // Prefix Title
				$repIndex++;
			}
		}

		if($this->notEmpty($this->encounter->supervisor_uid)){
			$supervisor = $this->u->load($this->encounter->supervisor_uid)->one();
			if($supervisor !== false){
				$provider = (object)$supervisor;
				$orc->setValue('12.1', $provider->npi, $repIndex); // NPI
				$orc->setValue('12.2.1', $provider->lname, $repIndex); // Last Name
				$orc->setValue('12.3', $provider->fname, $repIndex); // First Name
				$orc->setValue('12.4', $provider->mname, $repIndex); // Middle Name
				//$orc->setValue('7.5', $provider->suffix, $repIndex); // Suffix Sr. Jr
				$orc->setValue('12.6', $provider->title, $repIndex); // Prefix Title
				$repIndex++;
			}
		}
	}

	/**
	 * @param stdClass $observation
	 * @param int $sequence
	 * @throws Exception
	 */
	private function setOBR($observation, $sequence = 1) {

		$obr = $this->hl7->addSegment('OBR');
		$obr->setValue(1, $sequence);

		if($this->notEmpty($observation->id)){
			$obr->setValue('2', $observation->id);
		}
		if($this->notEmpty($observation->code)){
			$obr->setValue('4.1', $observation->code);
			$obr->setValue('4.2', $observation->code_text);
			$obr->setValue('4.3', $observation->code_type);
		}

		if($this->notEmpty($this->encounter->service_date)){
			$obr->setValue('7.1', $this->date($this->encounter->service_date));
		}

		$repIndex = 0;
		if($this->notEmpty($this->encounter->provider_uid)){
			$provider = $this->u->load($this->encounter->provider_uid)->one();
			if($provider !== false){
				$provider = (object)$provider;
				$obr->setValue('16.1', $provider->npi, $repIndex); // NPI
				$obr->setValue('16.2.1', $provider->lname, $repIndex); // Last Name
				$obr->setValue('16.3', $provider->fname, $repIndex); // First Name
				$obr->setValue('16.4', $provider->mname, $repIndex); // Middle Name
				//$orc->setValue('16.5', $provider->suffix, $repIndex); // Suffix Sr. Jr
				$obr->setValue('16.6', $provider->title, $repIndex); // Prefix Title
				$repIndex++;
			}
		}

		if($this->notEmpty($this->encounter->supervisor_uid)){
			$supervisor = $this->u->load($this->encounter->supervisor_uid)->one();
			if($supervisor !== false){
				$provider = (object)$supervisor;
				$obr->setValue('16.1', $provider->npi, $repIndex); // NPI
				$obr->setValue('16.2.1', $provider->lname, $repIndex); // Last Name
				$obr->setValue('16.3', $provider->fname, $repIndex); // First Name
				$obr->setValue('16.4', $provider->mname, $repIndex); // Middle Name
				//$orc->setValue('16.5', $provider->suffix, $repIndex); // Suffix Sr. Jr
				$obr->setValue('16.6', $provider->title, $repIndex); // Prefix Title
			}
		}
		if($this->notEmpty($observation->units)){
			$obr->setValue('27.1', $observation->code);
		}

		if($this->notEmpty($observation->code)){
			$obr->setValue('44.1', $observation->code);
			$obr->setValue('44.2', $observation->code_text);
			$obr->setValue('44.3', $observation->code_type);
		}

		if($this->notEmpty($observation->modifiers) && is_array($observation->modifiers)){
			$repIndex = 0;
			foreach($observation->modifiers as $modifier){
				$obr->setValue('45.1', $modifier, $repIndex);
				$repIndex++;
			}
		}
	}

	private function setDG1($diagnosis, $sequence = '1') {
		$diagnosis = explode(":", $diagnosis);
		$type = $this->encounter->close_date == '0000-00-00 00:00:00' ? 'W' : 'F';

		$dg1 = $this->hl7->addSegment('DG1');

		$dg1->setValue('1', $sequence);
		$dg1->setValue('2', $diagnosis[0]);
		$dg1->setValue('3.1', $diagnosis[1]);
		$dg1->setValue('6', $type);
	}

	private function setEncounter() {
		$this->encounter = $this->e->load($this->encounter)->one();
		if($this->encounter === false)
			return;
		$this->encounter = (object)$this->encounter;
	}

	public function saveMsg() {
		$foo = new stdClass();
		$foo->msg_type = $this->type;
		$foo->message = $this->hl7->getMessage();
		$foo->date_processed = date('Y-m-d H:i:s');
		$foo->isOutbound = true;
		$foo->status = 1; // 0 = hold, 1 = processing, 2 = queue, 3 = processed, 4 = error
		$foo->foreign_address = $this->to['address'] . (isset($this->to['port']) ? $this->to['port'] : '');
		$foo->foreign_facility = $this->to['facility'];
		$foo->foreign_application = $this->to['application_name'];
		$foo = $this->m->save($foo);
		$this->msg = (object)$foo['data'];
		return $this->msg;
	}

	private function Save() {
		$client = new HL7Client($this->to['address']);
		return $client->Save($this->msg->message);
	}

	public function Send() {
		$client = new HL7Client($this->to['address'], $this->to['port']);
		return $client->Send($this->msg->message);
	}

	public function getMessages($params) {
		return $this->m->load($params)->all();
	}

	public function getMessage($params) {
		return $this->m->load($params)->one();
	}

	public function getMessageById($id) {
		return $this->m->load($id)->one();
	}

	public function getRecipients($params) {
		return $this->c->load($params)->all();
	}

	private function date($date, $returnTime = true) {
		//$date = str_replace([' ',':','-'], '', $date);
		$dateObject = new DateTime($date);
		if($returnTime){
			return $dateObject->format('YmdHis');
		} else {
			return $dateObject->format('Ymd');
		}
	}

	private function phone($phone) {
		$phone = str_replace([
			' ',
			'(',
			')',
			'-'
		], '', $phone);
		return [
			'area' => substr($phone, 0, 3),
			'number' => substr($phone, 3, 9)
		];
	}

	private function notEmpty($data) {
		return isset($data) && ($data != '');
	}

	private function isPresent($var) {
		return isset($var) && $var != '';
	}

	private function cleanCodeType($code_type){

		switch($code_type){
			case 'ICD9':
			case 'ICD9-DX':
				$code_type = 'I9CDX';
				break;
		}

		return $code_type;
	}

	private function mapCode($code, $code_type, $type){

		if(isset($this->map_codes_types->{$type}) && $this->map_codes_types->{$type} != $code_type){
			$new_code_type = $this->map_codes_types->{$type};

			if($code_type == 'HL70189' && $new_code_type == 'CDCREC'){
				switch($code){
					case 'H':
						$code = '2135-2';
						$code_type = 'CDCREC';
						break;
					case 'N':
						$code = '2186-5';
						$code_type = 'CDCREC';
						break;
					case 'U':

						break;
				}
			}
		}

		return [
			'code' => $code,
		    'code_type' => $code_type
		];
	}
}
