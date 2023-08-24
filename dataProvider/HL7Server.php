<?php

/**
 * mdTimeLine (Electronic Health Records)
 * Copyright (C) 2016 Certun, LLC.
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
class HL7Server {

	/**
	 * @var HL7
	 */
	protected $hl7;
	/**
	 * @var HL7
	 */
	protected $ack;
	/**
	 * @var MatchaCUP
	 */
	protected $m;
	/**
	 * @var MatchaCUP
	 */
	protected $r;
	/**
	 * @var MatchaCUP
	 */
	protected $p;
	/**
	 * @var MatchaCUP
	 */
	protected $pa;
	/**
	 * @var MatchaCUP
	 */
	protected $pv;
	/**
	 * @var MatchaCUP
	 */
	protected $l;
    /**
     * @var MatchaCUP
     */
    protected $i;
    /**
     * @var MatchaCUP
     */
    protected $pi;
	/**
	 * @var MatchaCUP
	 */
	protected $s;
	/**
	 * @var bool
	 */
	public $ackStatus;
	/**
	 * @var string
	 */
	public $ackMessage;
	/**
	 * @var string
	 */
	protected $site;
	/**
	 * @var int
	 */
	protected $port;
	/**
	 * @var array|bool
	 */
	protected $recipient;
	/**
	 * @var array
	 */
	public $server;
	/**
	 * @var string
	 */
	protected $msg;

	/**
	 * @var MatchaCUP
	 */
	protected $pOrder;
	/**
	 * @var MatchaCUP
	 */
	protected $pResult;
	/**
	 * @var MatchaCUP
	 */
	protected $pObservation;

	/**
	 * @var DocumentHandler
	 */
	private $DocumentHandler;
	/**
	 * @var Facilities
	 */
	private $Facility;

    /**
     * @var \ReferringProviders
     */
    private $ReferringProviders;

	/**
	 * @var string
	 */
	protected $updateKey = 'pubpid';
	/**
	 * @var string pid || pubpid || account_no || account_no_alt
	 */
	protected $mergeKey = 'account_no';

    public $hl7_adt_visit_number = '$PID[18][1]';
    public $hl7_validate_referring_npi = false;
    public $hl7_orm_referring_id_column_field = 'npi';
    public $hl7_insurance_id_column_field = 'code';
    public $hl7_patient_insurance_id_column_field = 'code';
    public $hl7_insurance_type = '$IN1[15]';
    public $hl7_process_insurance_segments = false;
    public $hl7_adt_insurance_policy_number = '$IN1[36]';

    public $Hl7MessagePreProcessor;

    function __construct($port = 9000, $site = 'default') {
		$this->site = defined('site_id') ? site_id : $site;

		if(!defined('_GaiaEXEC'))
			define('_GaiaEXEC', 1);
		require_once(str_replace('\\', '/', dirname(dirname(__FILE__))) . '/registry.php');
		include_once(ROOT . "/sites/{$this->site}/conf.php");
		include_once(ROOT . '/classes/MatchaHelper.php');
		include_once(ROOT . '/classes/Encoding.php');
		include_once(ROOT . '/lib/HL7/HL7.php');
		include_once(ROOT . '/dataProvider/HL7ServerHandler.php');
		include_once(ROOT . '/dataProvider/PoolArea.php');
		include_once(ROOT . '/dataProvider/Merge.php');
		include_once(ROOT . '/dataProvider/Facilities.php');
		include_once(ROOT . '/dataProvider/Globals.php');
		include_once(ROOT . '/dataProvider/ReferringProviders.php');

		new MatchaHelper();

		/** HL7 Models */
		if(!isset($this->s))
			$this->s = MatchaModel::setSenchaModel('App.model.administration.HL7Server');
		if(!isset($this->m))
			$this->m = MatchaModel::setSenchaModel('App.model.administration.HL7Message');
		if(!isset($this->r))
			$this->r = MatchaModel::setSenchaModel('App.model.administration.HL7Client');
		if(!isset($this->i))
			$this->i = MatchaModel::setSenchaModel('App.model.administration.InsuranceCompany');

		/** Patient Model */
		if(!isset($this->p))
			$this->p = MatchaModel::setSenchaModel('App.model.patient.Patient');
		if(!isset($this->pa))
			$this->pa = MatchaModel::setSenchaModel('App.model.patient.PatientAccount');
		if(!isset($this->pi))
			$this->pi = MatchaModel::setSenchaModel('App.model.patient.Insurance');
		if(!isset($this->pv))
			$this->pv = MatchaModel::setSenchaModel('App.model.patient.Visit');

        if(!isset($this->l))
            $this->l = MatchaModel::setSenchaModel('App.model.administration.Location');

        if(!isset($this->ReferringProviders))
        $this->ReferringProviders = new ReferringProviders();


        /**
		 * User facilities
		 */
		$this->Facility = new Facilities();

		/** Order Models */
		if(!isset($this->pOrder))
			$this->pOrder = MatchaModel::setSenchaModel('App.model.patient.PatientsOrders');
		if(!isset($this->pResult))
			$this->pResult = MatchaModel::setSenchaModel('App.model.patient.PatientsOrderResult');
		if(!isset($this->pObservation))
			$this->pObservation = MatchaModel::setSenchaModel('App.model.patient.PatientsOrderObservation');
		$this->server = $this->getServerByPort($port);


        if(file_exists(site_path . '/hooks/Hl7MessagePreProcessor.php')){
            include_once (site_path . '/hooks/Hl7MessagePreProcessor.php');
            $this->Hl7MessagePreProcessor = new Hl7MessagePreProcessor();
        }


	}

	/**
	 * @param $port
	 *
	 * @return mixed
	 */
	protected function getServerByPort($port) {
		$this->s->addFilter('port', $port);
		$this->server = $this->s->load()->one();
		if($this->server !== false && isset($this->server['config'])){
			$this->server['config'] = parse_ini_string($this->server['config'], true, INI_SCANNER_RAW);
		}
		return $this->server;
	}

	public function Process($msg = '', $addSocketCharacters = true) {
		$this->msg = $msg;

		$this->ackStatus = 'AA';
		$this->ackMessage = '';

        $this->msg = ForceUTF8\Encoding::fixUTF8($this->msg);


        if(isset($this->Hl7MessagePreProcessor)){
            $this->msg = $this->Hl7MessagePreProcessor->PreProcessMessage($this->msg);
        }

		/**
		 * Parse the HL7 Message
		 */
		$hl7 = new HL7();
		$msg = $hl7->readMessage($this->msg);

		$application = $hl7->getSendingApplication();
		$application_id = $hl7->getSendingApplicationId();
		$facility = $hl7->getSendingFacility();
		$facility_id = $hl7->getSendingFacilityId();
		$version = $hl7->getMsgVersionId();
        $msg_type = $hl7->getMsgType();


		/**
		 * check HL7 version
		 */
		if($version != '2.5.1' && $version != '2.3' && $version != '2.4' && $version != '2.2' && $version != '2.3.1'){
			$this->ackStatus = 'AR';
			$this->ackMessage = 'HL7 version unsupported';
		}
		/**
		 * Check for IP address access
		 */
		$sql = 'SELECT * FROM `hl7_clients` WHERE (`application_iso_id` = :application_iso_id OR `application_name` = :application_name) AND `active` = 1';
		$this->recipient = $this->r->sql($sql)->one([
			':application_iso_id' => $application_id,
			':application_name' => $application
		]);
		if($this->recipient === false){
			$this->ackStatus = 'AR';
			$this->ackMessage = "This application '$application' Not Authorized";
		}
		/**
		 *
		 */
		if($msg === false){
			$this->ackStatus = 'AE';
			$this->ackMessage = 'Unable to parse HL7 message, please contact Support Desk';
		}

		$facilityRecord = $this->Facility->getFacility(['code' => $facility]);


        $hl7_client_config = <<<'INI_CONFIG'

[HL7]

hl7_pubpid_field = $PID[2][1]
hl7_account_no_field = $PID[3][0][1]
hl7_account_no_alt_field = $PID[4][0][1]
hl7_visit_no_field = $PID[18][1]
hl7_reference_no_field = $OBR[18]
hl7_department_code_field = $OBR[20]

[ORM]

hl7_allow_update_final_order = false
hl7_orm_validate_referring_npi = false
hl7_order_code_field = $ORC[2][1]
hl7_orm_accession_number_field = $OBR[2][1]
hl7_orm_specialty_code_field = explode(\'^\',$OBR[18])[0]


[ORU]

hl7_oru_validate_patient = false
hl7_report_code_field = $OBR[2][1]
hl7_oru_specialty_code_field = explode(\'^\',$OBR[18])[0]

INI_CONFIG;


        $hl7_client_config = parse_ini_string($hl7_client_config, true, INI_SCANNER_RAW);
        if(!empty($this->server['config'])){
            $this->parseIni($this->server['config']);
        }

		/**
		 *
		 */
		$msgRecord = new stdClass();
        $msgRecord->msg_id = $hl7->getMsgControlId();
        $msgRecord->msg_parent_id = null;
		$msgRecord->msg_type = $msg_type;
		$msgRecord->message = $this->msg;
		$msgRecord->foreign_facility = $hl7->getSendingFacility();
		$msgRecord->foreign_application = $hl7->getSendingApplication();
		$msgRecord->foreign_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$msgRecord->isOutbound = '0';
		$msgRecord->status = '2';
		$msgRecord->date_processed = date('Y-m-d H:i:s');
		$msgRecord = $this->m->save($msgRecord);
		$msgRecord = (array)$msgRecord['data'];

		error_log('HL7 Message: ' . $hl7->getMsgType() . '^' . $hl7->getMsgEventType());

		if($this->ackStatus == 'AA'){
			/**
			 *
			 */

			try {
				switch($msg_type) {
                    case 'ACK':
                        $this->ProcessACK($hl7, $msg, $msgRecord, $facilityRecord);
                        break;
					case 'ORU':
						$this->ProcessORU($hl7, $msg, $msgRecord, $facilityRecord);
						break;
					case 'ADT':
						$this->ProcessADT($hl7, $msg, $msgRecord, $facilityRecord);
						break;
					default:
						break;
				}
			}catch (Exception $e){
				error_log('HL7 Message core exception: ' . $e->getMessage());
			}

			$this->processHook($hl7, $msg, $msgRecord, $msg_type, $facilityRecord);
		}

		/**
		 * The first segment of the HL7 Message
		 */
		$ack = new HL7();
		$msh = $ack->addSegment('MSH');
		$msh->setValue('3.1', 'mdTimeLineEHR'); // Sending Application
		$msh->setValue('4.1', $this->Facility->getCurrentFacility(true)); // Sending Facility
		$msh->setValue('9.1', 'ACK');
		$msh->setValue('11.1', 'P'); // P = Production
		$msh->setValue('12.1', '2.5.1'); // HL7 version
		$msa = $ack->addSegment('MSA');
		$msa->setValue('1', $this->ackStatus); // AA = Positive acknowledgment, AE = Application error, AR = Application reject
		$msa->setValue('2', $hl7->getMsgControlId()); // Message Control ID from MSH
		$msa->setValue('3', $this->ackMessage); // Error Message
		$ackMsg = $ack->getMessage();

		$msgRecord['response'] = $ackMsg;
		$this->m->save((object)$msgRecord);

		// unset all the variables to release memory
		unset($ack, $hl7, $msg, $msgRecord, $oData, $result);

		return $addSocketCharacters ? "\v" . $ackMsg . chr(0x1c) . chr(0x0d) : $ackMsg;

	}

	protected function processHook(&$hl7, &$msg, &$msgRecord, $msg_type, $facilityRecord){

		if(
			isset($_SESSION['hooks']) &&
			isset($_SESSION['hooks']['HL7Server']) &&
			isset($_SESSION['hooks']['HL7Server']['Message']) &&
			isset($_SESSION['hooks']['HL7Server']['Message'][$msg_type])
		){
			foreach($_SESSION['hooks']['HL7Server']['Message'][$msg_type]['hooks'] as $i => $hook){
				include_once($hook['file']);
				$Hook = new $i();
				call_user_func_array(array(
					$Hook,
					$hook['method']
				), [
					&$this,
					&$hl7,
					&$msg,
					&$msgRecord,
					&$facilityRecord
				]);
			}
		}

	}

    protected function processACK($hl7, $msg, &$msgRecord, $facilityRecord){

        $msgRecord['msg_parent_id'] = $msg->data['MSA'][0][2];

    }

	/**
	 * @param $hl7 HL7
	 * @param $msg
	 * @param $msgRecord
	 * @param $facilityRecord
	 */
	protected function ProcessORU($hl7, $msg, &$msgRecord, $facilityRecord) {
		foreach($msg->data['PATIENT_RESULT'] AS $patient_result){
			$patient = isset($patient_result['PATIENT']) ? $patient_result['PATIENT'] : null;

			// Patient validation...
			$orderRecord = null;
			$orderId = null;
			$patientId = null;
			$patient_record = null;
			$ObservationResultsRelations = null;

			// Iterate through all the Observation Requests
			foreach($patient_result['ORDER_OBSERVATION'] AS $order){
				$orc = $order['ORC'];
				$obr = $order['OBR'];

				// If the OBR does not have a Parent Result value, is just a regular
				// Observation Request.
				if($obr[26][0] == ""){
					// Check for order number in mdTimeLine EHR
					if(!isset($orderId))
						$orderId = $orc[2][1];

					if(!isset($patientId))
						$patientId = $patient['PID'][3][0][1];

					if(!isset($patient_record))
						$patient_record = $this->getPatientByPid($patientId);

					if($patient_record == false){
						$this->ackStatus = 'AR';
						$this->ackMessage = "Unable to find patient record '$patientId'";
						break 2;
					}

					if(!isset($orderRecord)){
						$orderRecord = $this->pOrder->load(array(
								'id' => $orderId,
								'pid' => $patient_record['pid']
							))->one();
					}

					// id not found set the error and break twice to get out of all the loops
					if($orderRecord === false){
						$this->ackStatus = 'AR';
						$this->ackMessage = "Unable to find order number '$orderId' for patient '$patientId'";
						break 2;
					}

					$foo = new stdClass();
					$foo->pid = $patient_record['pid'];
					$foo->ordered_uid = $orderRecord['uid'];
					$foo->create_date = date('Y-m-d H:i:s');

					$foo->code = $obr[4][1] != '' ? $obr[4][1] : $orderRecord['code'];
					$foo->code_text = $obr[4][2] != '' ? $obr[4][2] : $orderRecord['code_text'];
					$foo->code_type = $obr[4][3] != '' ? $obr[4][3] : $orderRecord['code_type'];

					$foo->order_id = $orderId;
					$foo->performer_order_id = $obr[3][1];
					$foo->performer_name = $this->recipient['facility'];
					$foo->performer_address = $this->recipient['physical_address'];
					$foo->observation_date = $hl7->time($obr[7][1]);
					$foo->result_status = $obr[25];
					$foo->result_date = $hl7->time($obr[22][1]);

					if(is_array($obr[31])){
						$fo = array();
						foreach($obr[31] AS $dx){
							$fo[] = $dx[3] . ':' . $dx[1];
						}
						$foo->reason_code = implode(',', $fo);
					} else {
						$foo->reason_code = $obr[31][3] . ':' . $obr[31][1];
					}

					$order_notes = '';
					if(is_array($order['NTE'])){
						foreach($order['NTE'] as $nte){
							$order_notes .= $nte[3][0] . ' ';
						}
					}
					$foo->notes = $order_notes;

					// Specimen Segment
					if(isset($order['SPECIMEN']) && $order['SPECIMEN'] !== false){
						$spm = $order['SPECIMEN']['SPM'];
						$foo->specimen_code = $spm[4][3] == 'HL70490' ? $spm[4][3] : $spm[4][3];
						$foo->specimen_text = $spm[4][6] == 'HL70490' ? $spm[4][5] : $spm[4][2];
						$foo->specimen_code_type = $spm[4][1] == 'HL70490' ? $spm[4][1] : $spm[4][1];
						$foo->specimen_notes = $spm[21][3] == 'HL70490' ? $spm[21][2] : '';
					}

					//$foo->documentId = 'hl7|' . $msgRecord['id'];

					$foo->documentId = 'doc|' . $this->savePrintMessage($hl7, $patient_record['pid'], 'E-LABORATORY RESULTS');

					$rResult = (array)$this->pResult->save($foo);
					unset($foo);

					// Handle all the observations
					foreach($order['OBSERVATION'] AS $observation){

						//  Observations results and notes
						$obx = $observation['OBX'];
						$foo = new stdClass();

						// For the rest of the herd
						$foo->result_id = $rResult['id'];
						$foo->code = $obx[3][1];
						$foo->code_text = $obx[3][2];
						$foo->code_type = $obx[3][3];

						// Handle the dynamics of the value field
						// based on the OBX-2 value

						if($obx[2] == 'SN'){
							$foo->value = $obx[5][2];
						}elseif($obx[2] == 'CWE'){
							$foo->value = $obx[5][2];
						}else{
							$foo->value = $obx[5];
						}

						if(
							isset($obx[5]) &&
							is_array($obx[5]) &&
							isset($obx[5][1]) &&
							($obx[5][1] == '=' || $obx[5][1] == '>' || $obx[5][1] == '<')
						){
							$foo->value = $obx[5][1] . ' ' . $foo->value;
						}

						$foo->units = $obx[6][1];
						$foo->reference_rage = $obx[7];
						$foo->probability = $obx[9];
						$foo->abnormal_flag = $obx[8][0];
						$foo->nature_of_abnormal = $obx[10][0];
						$foo->observation_result_status = $obx[11];
						$foo->date_rage_values = $hl7->time($obx[12][1]);
						$foo->date_observation = $hl7->time($obx[14][1]);
						$foo->observer = trim($obx[16][0][2][1] . ' ' . $obx[16][0][3]);
						$foo->performing_org_name = $obx[23][1];
						$foo->performing_org_address = $obx[24][1][1] . ' ' . $obx[24][3] . ', ' . $obx[24][4] . ' ' . $obx[24][5];
						$foo->date_analysis = $hl7->time($obx[19][1]);

						$observation_notes = '';
						if(is_array($observation['NTE'])){
							foreach($observation['NTE'] as $nte){
								$observation_notes .= $nte[3][0] . ' ';
							}
						}
						$foo->notes = $observation_notes;

						// Save the observation result into the database
						$ObservationModel = $this->pObservation->save($foo);

						// Store the ID for future relationships
						$ObservationResultsRelations[$obx[1]] = [
							'ID' => $obx[1],
							'Observation' => $obx[3][1],
							'Parent_Id' => $ObservationModel->id
						];

						unset($foo);

					}

					// Change the order status to received
					$foo = new stdClass();
					$foo->id = $orderId;
					$foo->status = 'Received';
					$this->pOrder->save($foo);
					unset($foo);

				} elseif($obr[26][0] != "") {
					// If the OBR have a Parent Result value, it means that we
					// have to accommodate this Observation Request as a child of the previous
					// Observation Request
					// Check for order number in mdTimeLine EHR

					// Handle all the observations
					foreach($order['OBSERVATION'] AS $observation){

						//  Observations results and notes
						$obx = $observation['OBX'];
						$foo = new stdClass();


						// find parent_id
						if(
							isset($obr[26][2]) &&
							isset($ObservationResultsRelations[$obr[26][2]]) &&
							isset($ObservationResultsRelations[$obr[26][2]]['Parent_Id'])
						){
							$foo->parent_id = $ObservationResultsRelations[$obr[26][2]]['Parent_Id'];
						} else{
							$foo->parent_id = 0;
						}

						// For the rest of the herd
						$foo->code = $obx[3][1];
						$foo->code_text = $obx[3][2];
						$foo->code_type = $obx[3][3];
						$foo->value = $obx[5][2];
						$foo->units = $obx[6][1];
						$foo->reference_rage = $obx[7];
						$foo->probability = $obx[9];
						$foo->abnormal_flag = $obx[8][0];
						$foo->nature_of_abnormal = $obx[10][0];
						$foo->observation_result_status = $obx[11];
						$foo->date_rage_values = $hl7->time($obx[12][1]);
						$foo->date_observation = $hl7->time($obx[14][1]);
						$foo->observer = trim($obx[16][0][2][1] . ' ' . $obx[16][0][3]);
						$foo->performing_org_name = $obx[23][1];
						$foo->performing_org_address = $obx[24][1][1] . ' ' . $obx[24][3] . ', ' . $obx[24][4] . ' ' . $obx[24][5];
						$foo->date_analysis = $hl7->time($obx[19][1]);

						if(
							isset($obx[5]) &&
							is_array($obx[5]) &&
							isset($obx[5][1]) &&
							($obx[5][1] == '=' || $obx[5][1] == '>' || $obx[5][1] == '<')
						){
							$foo->value = $obx[5][1] . ' ' . $foo->value;
						}


						$observation_notes = '';
						if(is_array($observation['NTE'])){
							foreach($observation['NTE'] as $nte){
								$observation_notes .= $nte[3][0] . ' ';
							}
						}
						$foo->notes = $observation_notes;

						// Save the observation result into the database
						$this->pObservation->save($foo);
						unset($foo);
					}

					// Change the order status to received
					$foo = new stdClass();
					$foo->id = $orderId;
					$foo->status = 'Received';
					$this->pOrder->save($foo);
					unset($foo);
				}

			}
		}

		unset($patient, $rResult);
	}

	/**
	 * @param $hl7 HL7
	 * @param $pid int
	 * @param $title string
	 * @return int
	 */
	private function savePrintMessage($hl7, $pid, $title){

		if(!isset($this->DocumentHandler)){
			include_once (ROOT . '/dataProvider/DocumentHandler.php');
			$this->DocumentHandler = new DocumentHandler();
		}

		$msgType = $hl7->getMsgType();
		$printed_msg = $hl7->printMessage($title);

		$params = new stdClass();
		$params->pid = $pid;
		$params->eid = 0;
		$params->uid = 0;
		$params->facility_id = 0;
		$params->date = date('Y-m-d H:i:s');
		$params->docType = $msgType;
		$params->docTypeCode = $msgType;
		$params->document = base64_encode($printed_msg);
		$params->encrypted = false;
		$params->name = 'hl7_message.txt';
		$params->title = $title;

		$record = $this->DocumentHandler->addPatientDocument($params);

		return $record['data']->id;
	}


	protected function getPatientByPid($pid) {
		$sql = 'SELECT * FROM patient WHERE `pid`= :pid OR `pubpid`= :pubpid';
		return $this->p->sql($sql)->one([
			':pid' => $pid,
			':pubpid' => $pid
		]);
	}

	/**
	 * @param HL7 $hl7
	 * @param ADT|bool $msg
	 * @param stdClass $msgRecord
	 * @param array $facilityRecord
	 */
	protected function ProcessADT($hl7, $msg, $msgRecord, $facilityRecord) {
        if(!empty($this->server['config'])){
            $this->parseIni($this->server['config']);
        }

        $now = date('Y-m-d H:i:s');

		$evt = $hl7->getMsgEventType();

		if($evt == 'A01' || $evt == 'A02' || $evt == 'A03' || $evt == 'A04' || $evt == 'A05') {
			/**
			 * Trigger Events
             * ADT_A01 - Admit/Visit Notification
             * ADT_A02 - Transfer a Patient
             * ADT_A03 - Discharge/End Visit
             * ADT_A04 - Register a Patient
             * ADT_A05 - Pre-Admit a Patient
			 */

			$patient = $this->savePatient($now, $msg, $hl7, $facilityRecord);

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if(isset($msg->data['INSURANCE'])){
				$this->InsuranceGroupHandler($msg->data['INSURANCE'], $hl7, $patient);
			}

			if(isset($msg->data['PV1'])){
				$this->VisitHandler($msg->data , $hl7, $patient, $facilityRecord);
			}

			return;

		} elseif($evt == 'A08') {
			/**
			 * Update Patient Information
			 */
			$patient = $this->savePatient($now, $msg, $hl7, $facilityRecord);

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if(isset($msg->data['INSURANCE'])){
				$this->InsuranceGroupHandler($msg->data['INSURANCE'], $hl7, $patient);
			}
			return;
		} elseif($evt == 'A09') {
			/**
			 * Patient Departing - Tracking
			 * PV1-3 - Assigned Patient Location
			 * PV1-6 - Prior Patient Location
			 * PV1-11 - Temporary Location
			 * PV1-42 - Pending Location
			 * PV1-43 - Prior Temporary Location
			 */
			$PID = $msg->data['PID'];
			$PV1 = $msg->data['PV1'];

			$filter = array();
			if($PID[3][4][1] == $this->getAssigningAuthority()){
				$filter['pid'] = $PID[3][1];
			} else {
				$filter['pubpid'] = $PID[3][1];
			}

			$patient = $this->p->load($filter)->one();

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if($patient === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'Unable to find patient ' . $PID[3][1];
				return;
			}

			$newAreaCode = $PV1[3][1];
			//$oldAreaId = $PV1[6][1];

			$PoolArea = new PoolArea();
			$area = $PoolArea->getAreaByCode($newAreaCode);

			if($area === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'Unable to find Area code: ' . $newAreaCode;
				return;
			}

			$params = new stdClass();
			$params->pid = $patient['pid'];
			$params->sendTo = $area['id'];
			$PoolArea->sendPatientToPoolArea($params);
			unset($params);

			return;
		} elseif($evt == 'A10') {
			/**
			 * Patient Arriving - Tracking
			 * PV1-3  - As signed Patient Location
			 * PV1-6  - Prior Patient Location
			 * PV1-11 - Temporary Location
			 * PV1-43 - Prior Temporary Location
			 */
			$PID = $msg->data['PID'];
			$PV1 = $msg->data['PV1'];

			$filter = array();
			if($PID[3][0][4][1] == $this->getAssigningAuthority()){
				$filter['pid'] = $PID[3][0][1];
			} else {
				$filter['pubpid'] = $PID[3][0][1];
			}

			$patient = $this->p->load($filter)->one();

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if($patient === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'Unable to find patient ' . $PID[3][1];
				return;
			}

			$newAreaCode = $PV1[3][1];
			//$oldAreaId = $PV1[6][1];

			$PoolArea = new PoolArea();
			$area = $PoolArea->getAreaByCode($newAreaCode);

			if($area === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'Unable to find Area code: ' . $newAreaCode;
				return;
			}

			$params = new stdClass();
			$params->pid = $patient['pid'];
			$params->sendTo = $area['id'];
			$PoolArea->sendPatientToPoolArea($params);
			unset($params);

			return;
		} elseif($evt == 'A18') {
			/**
			 * Merge Patient Information
			 * PID-2.1 <= MRG-4.1
			 */
			$pid = $msg->data['PATIENT']['PID'][2][1];
			$mrg = $msg->data['PATIENT']['MRG'][4][1];
			$aPatient = $this->p->load(array('pubpid' => $pid))->one();
			$bPatient = $this->p->load(array('pubpid' => $mrg))->one();
			$this->MergeHandler($aPatient, $bPatient, $pid, $mrg);

			return;
		} elseif($evt == 'A28') {
			/**
			 * Add Person or Patient Information
			 * PID-2.1 <= MRG-4.1
			 */
			$PV1 = isset($msg->data['PV1']) ? $msg->data['PV1'] : null;
			$patientData = $this->PidToPatient($msg->data['PID'], $PV1, $hl7, $facilityRecord);
			$patientData['pubpid'] = $patientData['pid'];
			$patientData['pid'] = 0;
			$patient = $this->p->save((object)$patientData);

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if(isset($msg->data['INSURANCE'])){
				$this->InsuranceGroupHandler($msg->data['INSURANCE'], $hl7, $patient);
			}
			return;
		} elseif($evt == 'A29') {
			/**
			 * Delete Person Information
			 */
		} elseif($evt == 'A31') {
			/**
			 * Update Person Information
			 */

			$patient = $this->savePatient($now, $msg, $hl7, $facilityRecord, false);

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if($patient === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'Unable to find patient';
				return;
			}

			if(isset($msg->data['INSURANCE'])){
				$this->InsuranceGroupHandler($msg->data['INSURANCE'], $hl7, $patient);
			}
			return;
		} elseif($evt == 'A32') {
			/** Cancel Patient Arriving - Tracking **/

			return;
		} elseif($evt == 'A33') {
			/** Cancel Patient Departing - Tracking **/

			return;
		} elseif($evt == 'A34' || $evt == 'A39' || $evt == 'A40' || $evt == 'A41') {
			/**
			 * Merge Patient - Patient Identifier List
			 * PID-3.1 <= MRG-1.1
			 */

			if(!isset($msg->data['PATIENT']['MRG'])){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'MRG segment missing';
				return;
			}

			$pubpid = $msg->data['PATIENT']['PID'][2][1];

			if(isset($msg->data['PATIENT']['PID'][3][0])){
				$account_no = $msg->data['PATIENT']['PID'][3][0][1];
			}else{
				$account_no = '';
			}
			if(isset($msg->data['PATIENT']['PID'][4][0])){
				$account_no_alt = $msg->data['PATIENT']['PID'][4][0][1];
			}else{
				$account_no_alt = '';
			}
//			$account_no = $msg->data['PATIENT']['PID'][3][1];
//			$account_no_alt = $msg->data['PATIENT']['PID'][4][1];

			$merge_id = $msg->data['PATIENT']['MRG'][1][1];

			$patient = $this->savePatient($now, $msg, $hl7, $facilityRecord);

			if($patient !== false){
				$this->addHL7MessageReference($patient->pid, $msgRecord['id']);
			}

			if(isset($msg->data['INSURANCE'])){
				$this->InsuranceGroupHandler($msg->data['INSURANCE'], $hl7, $patient);
			}

			if($this->mergeKey == 'pubpid'){
//				$aPatient = $this->p->load(['pubpid' => $pubpid])->one();
				$bPatient = $this->p->load(['pubpid' => $merge_id])->one();
			}else{

				if($this->mergeKey == 'account_no'){
					$bAccount = $this->pa->load(['account_no' => $merge_id])->sortBy('created_date','DESC')->one();
//					$bAccount = $this->pa->load(['account_no' => $account_no])->sortBy('created_date','DESC')->one();
				}elseif($this->mergeKey == 'account_no_alt'){
					$bAccount = $this->pa->load(['account_no_alt' => $merge_id])->sortBy('created_date','DESC')->one();
//					$bAccount = $this->pa->load(['account_no_alt' => $account_no_alt])->sortBy('created_date','DESC')->one();
				}

				if(isset($bAccount) && $bAccount !== false){
					$bPatient = $this->p->load(['pid' => $bAccount['pid']])->one();
				}else{
					$bPatient = false;
				}

			}

			if($bPatient === false){
				$this->ackStatus = 'AR';
				$this->ackMessage = 'MRG patient not found';
				return;
			}

			$this->MergeHandler($patient, $bPatient, $pubpid, $merge_id);
			return;
		}

		/**
		 * Un handle event error
		 */
		$this->ackStatus = 'AR';
		$this->ackMessage = 'Unable to handle ADT_' . $evt;

	}

	private function addHL7MessageReference($pid, $msg_record_id){
		$conn = \Matcha::getConn();
		$sth = $conn->prepare("UPDATE hl7_messages SET reference = :reference WHERE id = :hl7_msg_id");
		$reference = 'patient:' . $pid;
		$hl7_msg_id = $msg_record_id;
		$sth->bindParam(':reference', $reference);
		$sth->bindParam(':hl7_msg_id', $hl7_msg_id);
		$sth->execute();
		unset($sth, $reference, $hl7_msg_id);
	}

	private function savePatient($now, $msg, &$hl7, $facilityRecord, $allow_insert = true){

		$PID = isset($msg->data['PATIENT']['PID']) ? $msg->data['PATIENT']['PID'] : $msg->data['PID'];

		if(isset($msg->data['VISIT']['PV1'])){
			$PV1 = $msg->data['VISIT']['PV1'];
		}elseif(isset($msg->data['PV1'])){
			$PV1 = $msg->data['PV1'];
		}else{
			$PV1 = null;
		}

		$patient_data = $this->PidToPatient($PID, $PV1, $hl7, $facilityRecord);
        $pubpid_issuer = isset($patient_data['pubpid_issuer']) && $patient_data['pubpid_issuer'] !== '' ? $patient_data['pubpid_issuer'] : null;
		$patient = $this->p->load(['pubpid' => $patient_data[$this->updateKey], 'pubpid_issuer' => $pubpid_issuer])->one();

		if($patient === false){

			if($allow_insert === false){
				return false;
			}

			$patient = (object) $patient_data;
			// force a new patient
			unset($patient->pid);

			$patient->create_date = $now;
			$patient->update_date = $now;
			$patient->create_uid = 0;
			$patient->update_uid = 0;
		}else{

			$patient = (array) $patient;
			$patient = array_merge($patient, $patient_data);

			if(!isset($patient['update_date'])){
				$patient['update_date'] = $now;
			}

			$patient['update_uid'] = 0;
		}

		$patient = (array) $patient;
		$accounts = isset($patient['accounts']) ? $patient['accounts'] : [];

		$patient =  $this->p->save((object)$patient);

		$this->pa->sql("INSERT INTO patient_account (pid, facility_id, account_no, account_no_alt, created_date)
				VALUES (:pid, :facility_id, :account_no, :account_no_alt, CURRENT_TIMESTAMP)
				ON DUPLICATE KEY UPDATE account_no = :account_no_2, account_no_alt = :account_no_alt_2, updated_date = CURRENT_TIMESTAMP");

		foreach ($accounts as $facility_id => $account){
			$this->pa->exec([
				':pid' => $patient->pid,
				':facility_id' => $facility_id,
				':account_no' => $account['number'],
				':account_no_alt' => $account['number_alt'],
				':account_no_2' => $account['number'],
				':account_no_alt_2' => $account['number_alt'],
			]);
		}

		return $patient;

	}

	/**
	 * @param array $PID
	 * @param array $PV1
	 * @param HL7 $hl7
	 * @param array|false $facilityRecord
	 *
	 * @return array
	 */
	public function PidToPatient($PID, $PV1, &$hl7, $facilityRecord = false) {
		$p = [];
		if($this->notEmpty($PID[2][1])){
			$p['pubpid'] = $PID[2][1]; // Patient ID (External ID)
		}

		if($this->notEmpty($PID[2][4][1])){
			$p['pubpid_issuer'] = $PID[2][4][1]; //  Assigning Authority
		}

		// handle accounts
		if($facilityRecord !== false){
			if($this->notEmpty($PID[3][0][1])){
				$p['accounts'][$facilityRecord['id']]['number'] = $PID[3][0][1]; // Patient ID (Internal ID)
			}
			if($this->notEmpty($PID[4][0][1])){
				$p['accounts'][$facilityRecord['id']]['number_alt'] = $PID[4][0][1]; // Patient ID (Internal ID)
			}
		}else{
			if(!isset($p['pubpid'])){
				if($this->notEmpty($PID[3][0][1])){
					$p['pubpid'] = $PID[3][0][1]; // Patient ID (Internal ID)
				}
			}
		}

		if($this->notEmpty($PID[5][0][2])){
			$p['fname'] = $PID[5][0][2]; // Patient Name...
		}
//		if($this->notEmpty($PID[5][0][3])){
			$p['mname'] = $PID[5][0][3]; //
//		}
		if($this->notEmpty($PID[5][0][1][1])){
			$p['lname'] = $PID[5][0][1][1]; //
		}
		if($this->notEmpty($PID[6][0][3])){
			$p['mothers_name'] = "{$PID[6][0][2]} {$PID[6][0][3]} {$PID[6][0][1][1]}"; // Mother’s Maiden Name
		}
		if($this->notEmpty($PID[7][1])){
			$p['DOB'] = $hl7->time($PID[7][1]); // Date/Time of Birth
		}
		if($this->notEmpty($PID[8])){
			$p['sex'] = $PID[8]; // Sex
		}
		if($this->notEmpty($PID[9][0][3])){
			$p['alias'] = "{$PID[9][0][2]} {$PID[9][0][3]} {$PID[9][0][1][1]}"; // Patient Alias
		}
		if($this->notEmpty($PID[10][0][1])){
			$p['race'] = $PID[10][0][1]; // Race
		}
		if($this->notEmpty($PID[11][0][1][1])){
			$p['postal_address'] = $PID[11][0][1][1]; // Patient Address
		}
		if($this->notEmpty($PID[11][0][2])){
			$p['postal_address_cont'] = $PID[11][0][2]; // Patient Address Cont
		}
		if($this->notEmpty($PID[11][0][3])){
			$p['postal_city'] = $PID[11][0][3]; //
		}
		if($this->notEmpty($PID[11][0][4])){
			$p['postal_state'] = $PID[11][0][4]; //
		}
		if($this->notEmpty($PID[11][0][5])){
			$p['postal_zip'] = $PID[11][0][5]; //
		}
		if($this->notEmpty($PID[11][0][6])){
			$p['postal_country'] = $PID[11][0][6]; // Country Code
		}
		if($this->notEmpty($PID[13][0][4])){
			$p['email'] = $PID[13][0][4]; // Email - Home
		}
		if($this->notEmpty($PID[13][0][7]) && ($PID[13][0][7] != $PID[13][0][1])){
			$p['phone_home'] = $this->phone("{$PID[13][0][7]}-{$PID[13][0][1]}"); // Phone Number – Home
		}elseif ($this->notEmpty($PID[13][0][1])){
            $p['phone_home'] = $this->phone($PID[13][0][1]); // Phone Number – Home
        }
        if($this->notEmpty($PID[14][0][7]) && ($PID[14][0][7] != $PID[14][0][1])){
            $p['phone_work'] = $this->phone("{$PID[14][0][7]}-{$PID[14][0][1]}"); // Phone Number – Home
        }elseif ($this->notEmpty($PID[14][0][1])){
            $p['phone_work'] = $this->phone($PID[14][0][1]); // Phone Number – Home
        }
		if($this->notEmpty($PID[15][1])){
			$p['language'] = $PID[15][1]; // Primary Language
		}
		if($this->notEmpty($PID[16][1])){
			$p['marital_status'] = $PID[16][1]; // Marital Status
		}
		//if($this->notEmpty($PID[17]))
		//  $p['00'] = $PID[17]; // Religion

		if($this->notEmpty($PID[18][1])){
			$p['pubaccount'] = $PID[18][1]; // Patient Account Number
		}
		if($this->notEmpty($PID[19])){
			$p['SS'] = $PID[19]; // SSN Number – Patient
		}
		if($this->notEmpty($PID[20][1])){
			$p['drivers_license'] = $PID[20][1]; // Driver’s License Number - Patient
		}
		if($this->notEmpty($PID[20][2])){
			$p['drivers_license_state'] = $PID[20][2]; // Driver’s License State - Patient
		}
		if($this->notEmpty($PID[20][3])){
			$p['drivers_license_exp'] = $PID[20][3]; // Driver’s License Exp Date - Patient
		}
		//if($this->notEmpty($PID[21]))
		//  $p['00'] = $PID[21]; // Mother’s Identifier

		if($this->notEmpty($PID[22][0][1])){
			$p['ethnicity'] = $PID[22][0][1]; // Ethnic Group
		}
		if($this->notEmpty($PID[23])){
			$p['birth_place'] = $PID[23]; // Birth Place
		}
		if($this->notEmpty($PID[24])){
			$p['birth_multiple'] = $PID[24]; // Multiple Birth Indicator
		}
		if($this->notEmpty($PID[25])){
			$p['birth_order'] = $PID[25]; // Birth Order
		}
		if($this->notEmpty($PID[26][0][1])){
			$p['citizenship'] = $PID[26][0][1]; // Citizenship
		}
		if($this->notEmpty($PID[27][1])){
			$p['is_veteran'] = $PID[27][1]; // Veterans Military Status
		}
		if($this->notEmpty($PID[27][1])){
			$p['death_date'] = $PID[29][1]; // Patient Death Date and Time
		}
		if($this->notEmpty($PID[30])){
			$p['deceased'] = $PID[30]; // Patient Death Indicator
		}
		if($this->notEmpty($PID[33][1])){
			$p['update_date'] = $hl7->time($PID[33][1]); // Last update time stamp
		}

		if($this->notEmpty($PID[18][1])){
			$p['last_visit_id'] = $PID[18][1]; // Last Visit ID
		}
		return $p;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	private function notEmpty($data) {
		return isset($data) && ($data != '' && $data != '""' && $data != '\'\'');
	}

	/**
	 * @param array $insGroups
	 * @param HL7 $hl7
	 * @param null $patient
	 * @return array|bool
	 */
	public function InsuranceGroupHandler($insGroups, $hl7, $patient = null) {
        $now = date('Y-m-d H:i:s');
		$insurances = [];

		// get out if flag is false
		if(!$this->hl7_process_insurance_segments){
			return $insurances;
		}

		foreach($insGroups as $groupIndex => $insuranceGroup){

            if($groupIndex === 0){
                $insuranceGroupType = 'P';
            }elseif($groupIndex === 1){
                $insuranceGroupType = 'S';
            }elseif($groupIndex === 2){
                $insuranceGroupType = 'C';
            }else{
                $insuranceGroupType = '';
            }

			foreach($insuranceGroup as $key => $insurance){
				if($insurance == false){
                    continue;
                }

				$insObj = new stdClass();

				if($key == 'IN1'){
					$this->IN1ToInsuranceObj($insurance, $hl7, $insObj, $insuranceGroupType);

				} elseif($key == 'IN2') {
					$this->IN2ToInsuranceObj($insurance, $hl7, $insObj);

				} elseif($key == 'IN3') {
					$this->IN3ToInsuranceObj($insurance, $hl7, $insObj);

				}

				if(empty($insObj->patient_insurance->company->{$this->hl7_insurance_id_column_field})){
				    continue;
                }

                $insObj->patient_insurance->pid = $patient->pid;
                $insObj->patient_insurance->{$this->hl7_patient_insurance_id_column_field} = $patient->pubpid . '~' . $insObj->patient_insurance->company->{$this->hl7_insurance_id_column_field};

				$insuranceCompanyRecord = $this->i->load([$this->hl7_insurance_id_column_field => $insObj->patient_insurance->company->{$this->hl7_insurance_id_column_field}])->one();

				if($insuranceCompanyRecord === false) {
                    $insObj->patient_insurance->company->create_uid = 0;
                    $insObj->patient_insurance->company->update_uid = 0;
                    $insObj->patient_insurance->company->create_date = $now;
                    $insObj->patient_insurance->company->update_date = $now;
                    $insuranceCompanyRecord = $this->i->save((object)$insObj->patient_insurance->company);

                    if(isset($insuranceCompanyRecord['data'])){
                        $insuranceCompanyRecord = (array) $insuranceCompanyRecord['data'];
                    }
                }

                $insObj->patient_insurance->insurance_id = $insuranceCompanyRecord['id'];

                $patientInsuranceRecord = $this->pi->load(array(
                    $this->hl7_patient_insurance_id_column_field => $insObj->patient_insurance->{$this->hl7_patient_insurance_id_column_field}
                ))->one();

                if($patientInsuranceRecord === false) {
                    $insObj->patient_insurance->create_uid = 0;
                    $insObj->patient_insurance->update_uid = 0;
                    $insObj->patient_insurance->create_date = $now;
                    $insObj->patient_insurance->update_date = $now;
                    $patientInsuranceRecord = $this->pi->save((object)$insObj->patient_insurance);
                }

				$insurances[] = $insObj;
			}
		}

		return $insurances;
	}

	/**
	 * @param array $IN1
	 * @param HL7 $hl7
	 * @param object $insObj
	 * @param string $INSURANCE_GROUP_TYPE
	 *
	 */
	protected function IN1ToInsuranceObj($IN1, $hl7, &$insObj, $INSURANCE_GROUP_TYPE) {

		$insObj->patient_insurance = isset($insObj->patient_insurance) ?
			$insObj->patient_insurance : new stdClass();

		$insObj->patient_insurance->company = isset($insObj->patient_insurance->company) ?
			$insObj->patient_insurance->company : new stdClass();


		/**
		 * Patient Insurance
		 * -----------------
		 *
		 * `patient_insurances`.`id`,
		 * `patient_insurances`.`code`,
		 * `patient_insurances`.`pid`,
		 * `patient_insurances`.`insurance_id`,
		 * `patient_insurances`.`insurance_type`,
		 *
		 * `patient_insurances`.`subscriber_ss`,
		 * `patient_insurances`.`subscriber_phone`,
		 * `patient_insurances`.`subscriber_employer`,
		 *
		 * `patient_insurances`.`cover_medical`,
		 * `patient_insurances`.`cover_dental`,
		 *
		 * Insurance Company
		 * -----------------
		 *
		 * `insurance_companies`.`id`,
		 *
		 * `insurance_companies`.`fax`,
		 * `insurance_companies`.`active`,
		 * `insurance_companies`.`dx_type`
		 *
		 */

		/**
		 * IN1-1 Set ID - IN1 (SI) 00426
		 */

		/**
		 * IN1-2 Insurance Plan ID (CE) 00368
		 *
		 */

		/**
		 * IN1-3 Insurance Company ID (CX) 00428
		 *
		 * `insurance_companies`.`code`, 3-1
		 */
		if($this->notEmpty($IN1[3][0][1])) {
            $insObj->patient_insurance->company->{$this->hl7_insurance_id_column_field} = $IN1[3][0][1];
        }else if($this->notEmpty($IN1[2][0])){
            $insObj->patient_insurance->company->{$this->hl7_insurance_id_column_field} = $IN1[2][0];
        }


		/**
		 * IN1-4 Insurance Company Name (XON) 00429
		 * `insurance_companies`.`name`, 4-1
		 *
		 */
		if($this->notEmpty($IN1[4][0][1])){
			$insObj->patient_insurance->company->name = $IN1[4][0][1];
		}

		/**
		 * IN1-5 Insurance Company Address (XAD) 00430
		 * `insurance_companies`.`address1`, 5-1
		 * `insurance_companies`.`address2`,
		 * `insurance_companies`.`city`, 5-3
		 * `insurance_companies`.`state`, 5-4
		 * `insurance_companies`.`zip_code`, 5-5
		 * `insurance_companies`.`country`, 5-6
		 */
		if($this->notEmpty($IN1[5][0][1][1])){
			$insObj->patient_insurance->company->address1 = $IN1[5][0][1][1];
		}
		if($this->notEmpty($IN1[5][0][3])){
			$insObj->patient_insurance->company->city = $IN1[5][0][3];
		}
		if($this->notEmpty($IN1[5][0][4])){
			$insObj->patient_insurance->company->state = $IN1[5][0][4];
		}
		if($this->notEmpty($IN1[5][0][5])){
			$insObj->patient_insurance->company->zip_code = $IN1[5][0][5];
		}
		if($this->notEmpty($IN1[5][0][6])){
			$insObj->patient_insurance->company->country = $IN1[5][0][6];
		}

		/**
		 * IN1-6 Insurance Co Contact Person (XPN) 00431
		 * `insurance_companies`.`attn`,
		 */
		if($this->notEmpty($IN1[6][0][1][1])){
			$insObj->patient_insurance->company->attn = $IN1[6][0][1][1];
		}else{
			$insObj->patient_insurance->company->attn = '';
		}
		if($this->notEmpty($IN1[6][0][2])){
			$insObj->patient_insurance->company->attn .= ', ' . $IN1[6][0][2];
		}

		/**
		 * IN1-7 Insurance Co Phone Number (XTN) 00432
		 * `insurance_companies`.`phone1`, 7-1
		 * `insurance_companies`.`phone2`, 7-2
		 */
		foreach($IN1[7] as $i => $phone){
			if($this->notEmpty($phone[1])){
				if($i === 0){
					$insObj->patient_insurance->company->phone1 =  $this->phone($phone[1]);
				}elseif($i === 1){
					$insObj->patient_insurance->company->phone2 = $this->phone($phone[1]);
				}
			}
		}


		/**
		 * IN1-8 Group Number (ST) 00433
		 * `patient_insurances`.`group_number`,  ????
		 */
		if($this->notEmpty($IN1[8])){
			$insObj->patient_insurance->group_number = $IN1[8];
		}

		/**
		 * IN1-9 Group Name (XON) 00434 ????
		 */

		/**
		 * IN1-10 Insured’s Group Emp. ID (CX) 00435
		 */

		/**
		 * IN1-11 Insured's Group Emp Name (XON) 00436
		 */


		/**
		 * IN1-12 Plan Effective Date (DT) 00437
		 * `patient_insurances`.`effective_date`,  12
		 */
		if($this->notEmpty($IN1[12])){
			$insObj->patient_insurance->effective_date = $hl7->time($IN1[12]);
		}
		/**
		 * IN1-13 Plan Expiration Date (DT) 00438
		 * `patient_insurances`.`expiration_date`, 13
		 */
		if($this->notEmpty($IN1[13])){
			$insObj->patient_insurance->expiration_date = $hl7->time($IN1[13]);
		}
		/**
		 * IN1-14 Authorization Information (AUI) 00439
		 */
        if($this->notEmpty($IN1[14][1])) {
            $insObj->patient_insurance->authorization_number = $IN1[14][1];
        }
		/**
		 * IN1-15 Plan Type (IS) 00440
		 */
        $insurance_type = '';
        eval("\$insurance_type = $this->hl7_insurance_type;");
        $insObj->patient_insurance->insurance_type = $insurance_type;
		/**
		 * IN1-16 Name of Insured (XPN) 00441
		 * `patient_insurances`.`subscriber_title`,
		 * `patient_insurances`.`subscriber_given_name`, 16-2
		 * `patient_insurances`.`subscriber_middle_name`,  16-3
		 * `patient_insurances`.`subscriber_surname`, 16-1-1
		 */
		if($this->notEmpty($IN1[16][0][1][1])){
			$insObj->patient_insurance->subscriber_surname = $IN1[16][0][1][1];
            $insObj->patient_insurance->card_last_name = $IN1[16][0][1][1];
		}
		if($this->notEmpty($IN1[16][0][2])){
			$insObj->patient_insurance->subscriber_given_name = $IN1[16][0][2];
            $insObj->patient_insurance->card_first_name = $IN1[16][0][2];
		}
		if($this->notEmpty($IN1[16][0][3])){
			$insObj->patient_insurance->subscriber_middle_name = $IN1[16][0][3];
            $insObj->patient_insurance->card_middle_name = $IN1[16][0][3];
		}


		/**
		 * IN1-17 Insured’s Relationship to Patient (CE) 00442
		 * `patient_insurances`.`subscriber_relationship`,  17-1
		 */
		if($this->notEmpty($IN1[17][1])){
			$insObj->patient_insurance->subscriber_relationship = $IN1[17][1];
		}

		/**
		 * IN1-18 Insured's Date of Birth (TS) 00443
		 * `patient_insurances`.`subscriber_dob`,  18-1
		 */
		if($this->notEmpty($IN1[18][1])){
			$insObj->patient_insurance->subscriber_dob = $hl7->time($IN1[18][1]);
		}

		/**
		 * IN1-19 Insured's Address (XAD) 00444
		 * `patient_insurances`.`subscriber_street`,
		 * `patient_insurances`.`subscriber_city`,
		 * `patient_insurances`.`subscriber_state`,
		 * `patient_insurances`.`subscriber_country`,
		 * `patient_insurances`.`subscriber_postal_code`,
		 */
		if($this->notEmpty($IN1[19][0][1][1])){
			$insObj->patient_insurance->subscriber_street = $IN1[19][0][1][1];
		}
		if($this->notEmpty($IN1[19][0][3])){
			$insObj->patient_insurance->subscriber_city = $IN1[19][0][3];
		}
		if($this->notEmpty($IN1[19][0][4])){
			$insObj->patient_insurance->subscriber_state = $IN1[19][0][4];
		}
		if($this->notEmpty($IN1[19][0][5])){
			$insObj->patient_insurance->subscriber_postal_code = $IN1[19][0][5];
		}
		if($this->notEmpty($IN1[19][0][6])){
			$insObj->patient_insurance->subscriber_country = $IN1[19][0][6];
		}

		/**
		 * IN1-20 Assignment of Benefits (IS) 00445
		 */

		/**
		 * IN1-21 Coordination of Benefits (IS) 00446
		 */

		/**
		 * IN1-22 Coord of Ben. Priority (ST) 00447
		 */

		/**
		 * IN1-24 Notice of Admission Date (DT) 00449
		 */

		/**
		 * IN1-25 Report of Eligibility Flag (ID) 00450
		 */

		/**
		 * IN1-26 Report of Eligibility Date (DT) 00451
		 */

		/**
		 * IN1-27 Release Information Code (IS) 00452
		 */

		/**
		 * IN1-28 Pre-admit Cert (PAC) (ST) 00453
		 */

		/**
		 * IN1-29 Verification Date/Time (TS) 00454
		 */

		/**
		 * IN1-30 Verification by (XCN) 00455
		 */

		/**
		 * IN1-31 Type of Agreement Code (IS) 00456
		 */

		/**
		 * IN1-32 Billing Status (IS) 00457
		 */

		/**
		 * IN1-33 Lifetime Reserve Days (NM) 00458
		 */

		/**
		 * IN1-34 Delay Before L.R. Day (NM) 00459
		 */

		/**
		 * IN1-35 Company Plan Code (IS) 00460
		 */

		/**
		 * IN1-36 Policy Number (ST) 00461
		 * `patient_insurances`.`policy_number`,  36
		 */
        $insurance_policy_number = '';
        eval("\$insurance_policy_number = $this->hl7_adt_insurance_policy_number;");
        $insObj->patient_insurance->policy_number = $insurance_policy_number;

		/**
		 * IN1-37 Policy Deductible (CP) 00462 37-1-1
		 */

		/**
		 * IN1-38 Policy Limit - Amount (CP) 00463
		 */

		/**
		 * IN1-39 Policy Limit - Days (NM) 00464
		 */

		/**
		 * IN1-40 Room Rate - Semi-Private (CP) 00465
		 */

		/**
		 * IN1-41 Room Rate - Private (CP) 00466
		 */

		/**
		 * IN1-42 Insured’s Employment Status (CE) 00467
		 */

		/**
		 * IN1-43 Insured’s Administrative Sex (IS) 00468
		 * `patient_insurances`.`subscriber_sex`,  43
		 */
		if($this->notEmpty($IN1[43])){
			$insObj->patient_insurance->subscriber_sex = $IN1[43];
		}

		/**
		 * IN1-44 Insured's Employer’s Address (XAD) 00469
		 */

		/**
		 * IN1-45 Verification Status (ST) 00470
		 */

		/**
		 * IN1-46 Prior Insurance Plan ID (IS) 00471
		 */

		/**
		 * IN1-47 Coverage Type (IS) 01227
		 */

		/**
		 * IN1-48 Handicap (IS) 00753
		 */

		/**
		 * IN1-49 Insured’s ID Number (CX) 01230
		 */

		/**
		 * IN1-50 Signature Code (IS) 01854
		 */

		/**
		 * IN1-51 Signature Code Date (DT) 01855
		 */

		/**
		 * IN1-52 Insured’s Birth Place (ST) 01899
		 */

		/**
		 * IN1-53 VIP Indicator (IS) 01852
		 */

        $insObj->patient_insurance->active = 1;

		return;
	}

	/**
	 * @param array $IN1
	 * @param HL7 $hl7
	 * @param object $insObj
	 *
	 */
	protected function IN2ToInsuranceObj($IN1, $hl7,  &$insObj) {

		return;
	}

	/**
	 * @param array $IN1
	 * @param HL7 $hl7
	 * @param object $insObj
	 *
	 */
	protected function IN3ToInsuranceObj($IN1, $hl7,  &$insObj) {



		return;
	}

	/**
	 * @return string
	 */
	private function getAssigningAuthority() {
		return 'GAIA-' . Matcha::getInstallationNumber();
	}

	/**
	 * @param $aPatient
	 * @param $bPatient
	 * @param $pid
	 * @param $mrg
	 */
	protected function MergeHandler($aPatient, $bPatient, $pid, $mrg) {
		if($aPatient === false){
			$this->ackStatus = 'AR';
			$this->ackMessage = 'Unable to find primary patient - ' . $pid;
			return;
		} elseif($bPatient === false) {
			$this->ackStatus = 'AR';
			$this->ackMessage = 'Unable to find merge patient - ' . $mrg;
			return;
		} elseif($aPatient['pid'] == $bPatient['pid']){
            $this->ackStatus = 'AR';
            $this->ackMessage = 'Unable to merge same patient PID  - ' . $mrg;
            return;
        }

		$aPatient = (array)$aPatient;
		$bPatient = (array)$bPatient;

		$filter = (object)[
			'filter' => [
				(object)[
					'property' => 'pid',
					'value' => $bPatient['pid']
				]
			]
		];

		$this->pa->destroy((object)[], $filter);


		$Merge = new Merge();
		$success = $Merge->merge($aPatient['pid'], $bPatient['pid']);
		unset($Merge);

		if($success === false){
			$this->ackStatus = 'AR';
			$this->ackMessage = 'Unable to merge patient ' . $aPatient['pid'] . ' <= ' . $bPatient['pid'];
			unset($aPatient, $bPatient);
			return;
		}
		unset($aPatient, $bPatient);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getServers($params) {

		$servers = $this->s->load($params)->all();
		foreach($servers['data'] as $i => $server){
			$handler = new HL7ServerHandler();
			$status = $handler->status($server);
			$servers['data'][$i]['online'] = $status['online'];
			unset($handler);
		}

		return $servers;
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function getServer($params) {
		$server = $this->s->load($params)->one();
		if($server === false || (isset($server['data']) && $server['data'] === false))
			return $server;

		$handler = new HL7ServerHandler();
		$status = $handler->status($server['port']);
		if(isset($server['data'])){
			$server['data']['online'] = $status['online'];
		} else {
			$server['online'] = $status['online'];
		}
		return $server;
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public function addServer($params) {
		return $this->s->save($params);
	}

	/**
	 * @param $params
	 *
	 * @return array
	 */
	public function updateServer($params) {
		return $this->s->save($params);
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function deleteServer($params) {
		return $this->s->destroy($params);
	}

	protected function getPatientByAccountNumber($account_no) {
		$sql = 'SELECT * FROM patient WHERE `pubaccount`=?';
		return $this->p->sql($sql)->one([$account_no]);
	}

	/**
	 * @param $phone
	 * @return array
	 */
	private function phone($phone) {
		$phone = str_replace([' ','(',')','-'], '', $phone);

		if(strlen($phone) == 10){
			return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3' ,$phone);
		}elseif(strlen($phone) == 7){
			return preg_replace('/(\d{3})(\d{4})/', '787-$1-$2' ,$phone);
		}else{
			return $phone;
		}

	}

	/**
	 * @param $hl7_server_config
	 * @param string $type  ALL|ADT|ORU
	 */
    function parseIni($hl7_server_config, $type = 'ALL'){

        if(isset($hl7_server_config['ALL'])){
            foreach ($hl7_server_config['ALL'] as $property => $value){
                if($value !== 'false' && $value !== 'true'){
                    $value = "'$value'";
                }
                eval("\$this->{$property} = $value;");
            }
        }

        if(isset($hl7_server_config[$type])) {
            foreach ($hl7_server_config[$type] as $property => $value) {
                if ($value !== 'false' && $value !== 'true') {
                    $value = "'$value'";
                }
                eval("\$this->{$property} = $value;");
            }
        }

    }

    public function VisitHandler($msg_data, $hl7, $patient, $facility_record){

        $PID = $msg_data['PID'];
        $PV1 = $msg_data['PV1'];
        $PV2 = $msg_data['PV2'];

        $visit_number = '';
        eval("\$visit_number = $this->hl7_adt_visit_number;");

        // get record by visit_number
        $visitRecord = $this->pv->load(['visit_number' => $visit_number])->one();

        if(isset($visitRecord) && $visitRecord !== false){
            $visitRecord = (object) $visitRecord;

        }else{
            $visitRecord = new stdClass();
        }

        $visitRecord->pid = $patient->pid;
        $visitRecord->visit_number = $visit_number;
        $visitRecord->facility_id = isset($facility_record['id']) ? $facility_record['id'] : null;

        if(isset($PV1[2]) && $PV1[2]) {
            $visitRecord->patient_class = $PV1[2];
        }

        if(isset($PV1[4]) && $PV1[4]) {
            $visitRecord->admission_type = $PV1[4];
        }

        if(isset($PV1[14][1]) && $PV1[14][1]) {
            $visitRecord->admit_source = $PV1[14][1];
        }

        if(isset($PV1[10]) && $PV1[10]) {
            $visitRecord->hospital_service = $PV1[10];
        }

        if(isset($PV1[44][1]) && $PV1[44][1]) {
            $visitRecord->admit_date = $hl7->time($PV1[44][1]);
        }

        if(isset($PV1[45][1]) && $PV1[45][1]) {
            $visitRecord->discharge_date = $hl7->time($PV1[45][1]);
        }

        if(isset($PV1[36]) && $PV1[36]) {
            $visitRecord->discharge_disposition = $PV1[36];
        }

        //attending
        if(isset($PV1[7])){
            $attending = $this->referringHandler($PV1[7]);

            if($attending !== false){
                $visitRecord->attending_id = $attending['id'];
            }
        }

        //admitting doctor
        if(isset($PV1[17])){
            $admitting = $this->referringHandler($PV1[17]);

            if($admitting !== false){
                $visitRecord->admitting_id = $admitting['id'];
            }
        }

        //referring
        if(isset($PV1[8])){
            $referring = $this->referringHandler($PV1[8]);

            if($referring !== false){
                $visitRecord->referring_id = $referring['id'];
            }
        }

        //consulting
        if(isset($PV1[9]) && count($PV1[9]) > 0 ){

            foreach ($PV1[9] as $i => $consulting) {

                if($i === 3){
                    break;
                }

                $column_name = 'consulting' . ($i + 1) . '_id';
                $c = $this->referringHandler($consulting);
                if($c !== false){
                    $visitRecord->{$column_name} = $c['id'];
                }
            }
        }


        //Assigned Location
        if(isset($PV1[3][1]) && $PV1[3][1]) {
            $location_record = $this->l->load(['code' => $PV1[3][1]])->one();
            if(isset($location_record) && $location_record !== false){
                $visitRecord->assigned_location = $location_record['id'];
            }
        }

        //Assigned Zone
        if(isset($PV1[3][2]) && !empty($PV1[3][2])){
            $visitRecord->assigned_zone = $PV1[3][2] . '^' . $PV1[3][3];
        }



        //Prior Location
        if(isset($PV1[6][1]) && $PV1[6][1]) {
            $location_record = $this->l->load(['code' => $PV1[6][1]])->one();
            if(isset($location_record) && $location_record !== false){
                $visitRecord->prior_location = $location_record['id'];
            }
        }

        //Prior Zone
        if(isset($PV1[6][2]) && !empty($PV1[6][2])){
            $visitRecord->prior_zone = $PV1[6][2] . '^' . $PV1[6][3];
        }

        $visitRecord = $this->pv->save($visitRecord);

    }

    /**
     * @param $referring array
     * @return array|bool
     */
    public function referringHandler($referring) {

        if($referring[0][1] != ''){

            $referring[0][1] = trim($referring[0][1]);

            /**
             * Validate NPI
             */
            if($this->hl7_validate_referring_npi && !$this->isNpiValid($referring[0][1])){
                return false;
            }

            $referringRecord = $this->ReferringProviders->getReferringProvider([$this->hl7_orm_referring_id_column_field => $referring[0][1]]);
            if($referringRecord !== false){
                return $referringRecord;
            }

            $referringRecord = new \stdClass();
//            $referringRecord->npi = $referring[0][1];
            $referringRecord->{$this->hl7_orm_referring_id_column_field} = $referring[0][1];
            $referringRecord->lname = $referring[0][2][1];
            $referringRecord->fname = $referring[0][3];
            $referringRecord->mname = $referring[0][4];
            $referringRecord->title = $referring[0][6];
            $referringRecord = $this->ReferringProviders->addReferringProvider($referringRecord);

            if($referringRecord !== false){
                return (array)$referringRecord['data'];
            }

            return $referring;

        } else {

            return false;

//            $referring = $this->ReferringProviders->getReferringProvider(['npi' => '0000000000']);
//            if($referring !== false){
//                return $referring;
//            }
//
//            $referring = new \stdClass();
//            $referring->npi = '0000000000';
//            $referring->lname = 'NON-STAFF';
//            $referring->fname = 'NON-STAFF';
//            $referring->mname = '';
//            $referring->title = '';
//            $referring = $this->ReferringProviders->addReferringProvider($referring);
//            if($referring !== false){
//                return (array)$referring;
//            }
        }
    }

    private function isNpiValid($npi) {

        $tmp = null;
        $sum = null;
        $i = strlen($npi);

        if(!is_numeric($npi)) return false;

        if(($i == 15) && (substr($npi, 0, 5) == "80840")){
            $sum = 0;
        } else if($i == 10){
            $sum = 24;
        } else {
            return false;
        }

        $j = 0;
        while($i--) {
            if(is_nan($npi[$i])){
                return false;
            }
            $tmp = $npi[$i] - '0';
            if($j++ & 1){
                if(($tmp <<= 1) > 9){
                    $tmp -= 10;
                    $tmp++;
                }
            }
            $sum += $tmp;
        }

        if($sum % 10){
            return false;
        } else {
            return true;
        }

    }

}
