<?php

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 3/2/17
 * Time: 5:03 AM
 */
class HL7Printer {

	static $dates_format = 'F j, Y, g:i:s a';
	static $dates_format_compact = 'Y-m-d H:i:s';

	static function printMessage($message, $title){
		$title = strtoupper($title);

		if($title != ''){
			$text = <<<TITLE
| {$title}
|

TITLE;

		}else{
			$text = '';
		}


		foreach ($message->data as $key => $data){

			if($key == 'MSH'){
				$text .= self::printMSH($data);
			}elseif($key == 'PATIENT_RESULT'){
				$text .= self::printPatientResults($data);
			}


		}



		return $text;
	}

	/**
	 * @param $data
	 * @return string
	 */
	private static function printMSH($data){


		$msg_date = $data[7][1];
		if($msg_date != ''){
			$msg_date = date(self::$dates_format, $msg_date);
		}


		$DATE = str_pad('DATE: ' . ($msg_date), 60);
		$MSG_TYPE = 'MSG TYPE: '. ($data[0]);
		$CONTROL_ID = 'CONTROL ID: ' . ($data[10]);
		$SENDING_APP = str_pad('SENDING APPLICATION: ' . ($data[3][1]), 60);
		$SENDING_FACILITY = str_pad('SENDING: FACILITY' . ($data[4][1]), 60);
		$RECEIVING_APP = 'RECEIVING APPLICATION: ' . ($data[5][1]);
		$RECEIVING_FACILITY = 'RECEIVING FACILITY: ' . ($data[6][1]);

		$text = <<<MSH
|----------------------------------------------------------------------------------------------------------------------------------------------------
| MSG HEADER
|----------------------------------------------------------------------------------------------------------------------------------------------------
| $MSG_TYPE
| $DATE| $CONTROL_ID
| $SENDING_APP| $RECEIVING_APP
| $SENDING_FACILITY| $RECEIVING_FACILITY
|

MSH;

		return $text;
	}

	private static function printPID($data){

	}

	private static function printPatientResults($patient_results){

		$PATIENT_RESULTS = '';

		foreach ($patient_results as $index => $patient_result){
			$PATIENT_RESULTS .= self::printPatientResult($patient_result, $index + 1);
		}

		$text = <<<RESULTS
|----------------------------------------------------------------------------------------------------------------------------------------------------
| PATIENT RESULTS
|----------------------------------------------------------------------------------------------------------------------------------------------------
|
$PATIENT_RESULTS
RESULTS;

		return $text;
	}

	private static function printPatientResult($patient_result, $result_number){

		$PATIENT_RESULT = '';

		$PATIENT_RESULT .= self::printPatient($patient_result['PATIENT']);
		$PATIENT_RESULT .= self::printOrderObservations($patient_result['ORDER_OBSERVATION']);

		$text = <<<RESULT
| {$result_number}. RESULT
| ---------------------------------------------------------------------------------------------------------------------------------------------------
|
$PATIENT_RESULT
RESULT;


		return $text;
	}


	private static function printPatient($patient)
	{
		$PATIENT = '';

		$patient_external_id = $patient['PID'][2][1];
		$patient_internal_id = $patient['PID'][3][0][1];
		$patient_alt_id = $patient['PID'][4][0][1];
		$patient_sex = str_pad('SEX: ' . $patient['PID'][8], 60);
		$patient_dob = str_pad('DOB: ' . $patient['PID'][7][1], 60);
		$patient_race = 'RACE: ' . $patient['PID'][10][0][2];
		$patient_lang = 'LANGUAGE: ' . $patient['PID'][15][1];

		$patient_name =  $patient['PID'][5][0][2] . ' ';
		if($patient['PID'][5][0][7] != ''){
			$patient_name .= $patient['PID'][5][0][7] . ' ';
		}
		$patient_name .= $patient['PID'][5][0][1][1];
		$patient_name = str_pad('NAME: ' . $patient_name, 60);
		$patient_dis = "INT/EXT/ALT IDs: {$patient_internal_id}/{$patient_external_id}/{$patient_alt_id}";

		$text = <<<PATIENT
|   -------------------------------------------------------------------------------------------------------------------------------------------------
|   PATIENT
|   -------------------------------------------------------------------------------------------------------------------------------------------------
|   {$patient_name}| {$patient_dis}
|   {$patient_sex}| {$patient_race}
|   {$patient_dob}| {$patient_lang}
|   -------------------------------------------------------------------------------------------------------------------------------------------------
|

PATIENT;
		return $text;
	}


	private static function printOrderObservations($order_observations)
	{

		$OBSERVATIONS = '';

		foreach ($order_observations as $index => $order_observation){

			if($order_observation){
				$OBSERVATIONS .= self::printOrderObservation($order_observation, $index + 1);
			}



		}

		$text = <<<OBSERVATIONS
|   -------------------------------------------------------------------------------------------------------------------------------------------------
|   ORDERS
|   -------------------------------------------------------------------------------------------------------------------------------------------------
|
$OBSERVATIONS

OBSERVATIONS;
		return $text;
	}

	private static function printOrderObservation($order_observation, $observation_number)
	{

		$OBSERVATIONS = '';
		$PERFORMING_ORGANIZATION = '';

		/**
		 * REPORT TEST DATA
		 */
		if($order_observation['OBR'][4][2] != ''){
			$test_name = $order_observation['OBR'][4][2];
		}else{
			$test_name = $order_observation['OBR'][4][1];
		}
		$test_report_number = $order_observation['OBR'][3][1];
		$test_report_date = $order_observation['OBR'][7][1];
		if($test_report_date != ''){
			$test_report_date = date(self::$dates_format, strtotime($test_report_date));
		}




		foreach ($order_observation['OBSERVATION'] as $observation){

			if(!isset($observation['OBX'])) continue;





			if($OBSERVATIONS == ''){
				$OBSERVATIONS .= <<<HEAD
				
|       |============================================================================================================================================
|       | CODE    | DESCRIPTION                   | RESULT         | UOM            | RANGE          | ABN  | STATUS  | DATE/TIME           | COMMENT
|       |============================================================================================================================================
HEAD;


			}

			$code = str_pad($observation['OBX'][3][1], 8);
			$description = str_pad($observation['OBX'][3][2], 30);
			$result = str_pad($observation['OBX'][5][4], 15);
			$uom = str_pad($observation['OBX'][6][1], 15);
			$range = str_pad($observation['OBX'][7], 15);
			$abnormal_flag = str_pad($observation['OBX'][8][0], 5);
			$status = str_pad($observation['OBX'][11], 8);

			$obs_date = $observation['OBX'][14][1];
			if($obs_date != ''){
				$obs_date = date(self::$dates_format_compact, strtotime($obs_date));
			}
			$obs_date = str_pad($obs_date, 20);

			if($observation['NTE'] && is_array($observation['NTE'])){




			}else{
				$comment = str_pad('NONE', 60);
			}





			$OBSERVATIONS .= <<<HEAD

|       | {$code}| {$description}| {$result}| {$uom}| {$range}| {$abnormal_flag}| {$status}| {$obs_date}| {$comment}
|       |--------------------------------------------------------------------------------------------------------------------------------------------
HEAD;


		}


		if(isset($order_observation['SPECIMEN']) && isset($order_observation['SPECIMEN']['SPM'])){

			$type = $order_observation['SPECIMEN']['SPM'][4][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][4][2];

			$collection_start = $order_observation['SPECIMEN']['SPM'][17][1][1];
			$collection_stop = $order_observation['SPECIMEN']['SPM'][17][2][1];

			if($collection_start != ''){
				$collection_start = date(self::$dates_format, strtotime($collection_start));
			}else{
				$collection_start = 'UNKNOWN';
			}

			if($collection_stop != ''){
				$collection_stop = date(self::$dates_format, strtotime($collection_stop));
			}else{
				$collection_stop = 'UNKNOWN';
			}

			$reject_reason = $order_observation['SPECIMEN']['SPM'][21][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][21][2];
			$appropriateness = $order_observation['SPECIMEN']['SPM'][23][1] . ' - '. $order_observation['SPECIMEN']['SPM'][23][2];
			$condition = $order_observation['SPECIMEN']['SPM'][24][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][24][2];
			$notes = $order_observation['SPECIMEN']['SPM'][21][3];

			$SPECIMEN = <<<SPM
TYPE: {$type}
|       COLLECTION START DATE: {$collection_start}
|       COLLECTION STOP DATE: {$collection_stop}
|       REJECT REASON: {$reject_reason}
|       APPROPRIATENESS: {$appropriateness}
|       CONDITION: {$condition}
|       NOTES: {$notes}
SPM;

		}else{
			$SPECIMEN = 'NONE';
		}

		$text = <<<OBSERVATIONS
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       {$observation_number}. ORDER/TEST
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       NAME: $test_name
|       REPORT DATE: $test_report_date
|       REPORT NUMBER: $test_report_number
|
|       OBSERVATIONS:
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       $OBSERVATIONS
| 
|       SPECIMEN
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       $SPECIMEN
|
|       PERFORMING_ORGANIZATIONS:
|       ---------------------------------------------------------------------------------------------------------------------------------------------
| 
|       $PERFORMING_ORGANIZATION

OBSERVATIONS;
		return $text;
	}
}