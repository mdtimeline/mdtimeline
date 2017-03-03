<?php

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 3/2/17
 * Time: 5:03 AM
 */
class HL7Printer {

    /**
     * @var string
     */
	static $dates_format = 'F j, Y, g:i:s a';

    /**
     * @var string
     */
	static $dates_format_compact = 'Y-m-d H:i:s';

    /**
     * @var array
     */
    static $message;

    /**
     * @var array
     */
    static $children_reports = [];

	static function printMessage($message, $title){
		$title = strtoupper($title);

		if($title != ''){
			$text = <<<TITLE
|
| {$title}
|

TITLE;

		}else{
			$text = '';
		}


        self::$message = $message;

		foreach ($message->data as $key => $data){
			if($key == 'MSH'){
				$text .= self::printMSH($data);
			}elseif($key == 'PATIENT_RESULT'){
				$text .= self::printPatientResults($data);
			}
		}

		$text = rtrim($text);

		$text .= <<<FOOTER

|----------------------------------------------------------------------------------------------------------------------------------------------------
| {$title} END!
|----------------------------------------------------------------------------------------------------------------------------------------------------
|----------------------------------------------------------------------------------------------------------------------------------------------------
FOOTER;

		return $text;
	}

	/**
	 * @param $data
	 * @return string
	 */
	private static function printMSH($data){

		$msg_date = $data[7][1];
		if($msg_date != ''){
			$msg_date = date(self::$dates_format, strtotime($msg_date));
		}

		$DATE = str_pad('DATE: ' . ($msg_date), 62);
		$MSG_TYPE = 'MSG TYPE: '. ($data[9][3]);
		$CONTROL_ID = 'CONTROL ID: ' . ($data[10]);
		$SENDING_APP = str_pad('SENDING APPLICATION: ' . ($data[3][1]), 62);
		$SENDING_FACILITY = str_pad('SENDING: FACILITY' . ($data[4][1]), 62);
		$RECEIVING_APP = 'RECEIVING APPLICATION: ' . ($data[5][1]);
		$RECEIVING_FACILITY = 'RECEIVING FACILITY: ' . ($data[6][1]);

		$text = <<<MSH
|----------------------------------------------------------------------------------------------------------------------------------------------------
| HL7 MSG HEADER
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
		$patient_dob = str_pad('DOB: ' . date('F j, Y',$patient['PID'][7][1]), 60);
		$patient_race = 'RACE: ' . $patient['PID'][10][0][2];
		$patient_lang = 'LANGUAGE: ' . $patient['PID'][15][1];

		$patient_name =  $patient['PID'][5][0][2] . ' ';
		if($patient['PID'][5][0][3] != ''){
			$patient_name .= $patient['PID'][5][0][3] . ' ';
		}

		$patient_name .= $patient['PID'][5][0][1][1] . ' ';

        if($patient['PID'][5][0][4] != ''){
            $patient_name .= $patient['PID'][5][0][4] . ' ';
        }

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
				$OBSERVATIONS .= self::printOrderObservation($order_observation, $index + 1, $order_observations);
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

	private static function findParentObservation($order_observations, $order_observation){

	    $obx_index = [];
        $parent_id = $order_observation['OBR'][26][1][1];
        $parent_sub_id = $order_observation['OBR'][26][2];

        if($parent_id == '' || $parent_sub_id == '') return false;

	    foreach ($order_observations as $observations){
	        foreach ($observations['OBSERVATION'] as $buff){
                if ($buff['OBX'][3][1] == $parent_id && $buff['OBX'][4] == $parent_sub_id){
                    return $buff;
                }
            }
        }

        return false;
    }


	private static function printOrderObservation($order_observation, $observation_number, $order_observations)
	{
		/**
		 * REPORT TEST DATA
		 */
		if($order_observation['OBR'][4][2] != ''){
			$test_name = $order_observation['OBR'][4][2];
		}else{
			$test_name = $order_observation['OBR'][4][1];
		}
		$test_report_number = $order_observation['OBR'][3][1];
		$test_report_date = $order_observation['OBR'][22][1];
		if($test_report_date != ''){
			$test_report_date = date(self::$dates_format, strtotime($test_report_date));
		}


		$placer_order_no = str_pad('PLACER ORDER NO.: ' . $order_observation['OBR'][2][1], 56);
		$filler_order_no = str_pad('FILLER ORDER NO.: ' . $order_observation['ORC'][3][1], 56);
		$placer_group_no = str_pad('PLACER GROUP NO.: ' . $order_observation['ORC'][4][1], 56);

		$ordering_provider_id = $order_observation['ORC'][12][0][9][1] . ' - ' . $order_observation['ORC'][12][0][9][3];
        $ordering_provider_name = '';

        // prefix
        if($order_observation['ORC'][12][0][6] != ''){
            $ordering_provider_name .= $order_observation['ORC'][12][0][6] . ' ';
        }

        // last
        $ordering_provider_name .=  $order_observation['ORC'][12][0][2][1] . ', ';

        // first
        if($order_observation['ORC'][12][0][3] != ''){
            $ordering_provider_name .= $order_observation['ORC'][12][0][3] . ' ';
        }

        // middle
        if($order_observation['ORC'][12][0][4] != '') {
            $ordering_provider_name .= $order_observation['ORC'][12][0][4] . ' ';
        }

        // suffix
        if($order_observation['ORC'][12][0][5] != ''){
            $ordering_provider_name .= $order_observation['ORC'][12][0][5] . ' ';
        }

        $ordering_provider_id = 'PROVIDER ID: ' . $ordering_provider_id;
		$ordering_provider_name = 'PROVIDER NAME: ' . $ordering_provider_name;

		$line_len = 30;
		$rows = [];
		$columns_lens = [
			'code' => 6,
			'description' => 10,
			'result' => 10,
			'uom' => 7,
			'range' => 10,
			'abnormal_flag' => 5,
			'status' => 8,
			'obs_date' => 10,
			'note' => 0
		];

		$PERFORMING_ORGANIZATIONS = [];

		$report_notes = '';

		if(isset($order_observation['NTE']) && is_array($order_observation['NTE'])) {
            foreach ($order_observation['NTE'] as $index => $note) {
                if ($index > 0) {
                    $report_notes .= PHP_EOL . '|              ';
                }
                $report_notes .= $index + 1 . '. ' . $note[3][0];
            }
        }

		foreach ($order_observation['OBSERVATION'] as $observation){
			if(!isset($observation['OBX'])) continue;
			$row['code'] = $observation['OBX'][3][1];
			$row['description'] = $observation['OBX'][3][2];

			if($observation['OBX'][2] == 'SN'){
				$row['result'] = $observation['OBX'][5][2];
			}elseif($observation['OBX'][2] == 'CWE'){
				$row['result'] = $observation['OBX'][5][2];
			}else{
				$row['result'] = $observation['OBX'][5];
			}


			$row['uom'] = $observation['OBX'][6][1];
			$row['range'] = $observation['OBX'][7];
			$row['abnormal_flag'] = $observation['OBX'][8][0];
			$row['status'] = $observation['OBX'][11];
			$row['obs_date'] = $observation['OBX'][14][1];

			if($row['obs_date'] != ''){
				$row['obs_date'] = date(self::$dates_format_compact, strtotime($row['obs_date']));
			}

			if($observation['NTE'] && is_array($observation['NTE'])){
				if(is_array($observation['NTE'])){
					$row['note'] = '';
					foreach($observation['NTE'] as $nte){
						$row['note'] .= $nte[3][0] . ' ';
					}
				}
			}else{
				$row['note'] = '- ';
			}

			foreach ($row as $column => $value){
				$len = strlen($value);
				if($columns_lens[$column] < $len){
					$columns_lens[$column] = $len + 1;
				}
			}

			$organization_key = $observation['OBX'][23][1] != '' ? str_replace(' ', '_' ,$observation['OBX'][23][1])  : 'UNKNOWN';

			if(!array_key_exists($organization_key, $PERFORMING_ORGANIZATIONS)){

				$organization_name = $observation['OBX'][23][1];
				$organization_address = $observation['OBX'][24][1][1];
				$organization_city = $observation['OBX'][24][3];
				$organization_state = $observation['OBX'][24][4];
				$organization_zip = $observation['OBX'][24][5];
				$organization_country = $observation['OBX'][24][6];
				$organization_parish = $observation['OBX'][24][9];

				$performing_organization_director_id = $observation['OBX'][25][1];
				$performing_organization_director_name = '';

				if($observation['OBX'][25][6] != ''){
					$performing_organization_director_name .= $observation['OBX'][25][6] . ' ';
				}

				if($observation['OBX'][25][2][1] != ''){
					$performing_organization_director_name .= $observation['OBX'][25][2][1] . ' ';
				}

				if($observation['OBX'][25][3] != ''){
					$performing_organization_director_name .= $observation['OBX'][25][3] . ' ';
				}

				if($observation['OBX'][25][4] != ''){
					$performing_organization_director_name .= $observation['OBX'][25][4] . ' ';
				}

				$org_text = <<<ORG
|       CONTACT: $performing_organization_director_name
|       NAME: {$organization_name}
|       STREET: {$organization_address}
|       CITY: {$organization_city}
|       SATE: {$organization_state}
|       ZIP CODE: {$organization_zip}
|       COUNTRY: {$organization_country}
|       COUNTY/PARISH CODE: {$organization_parish}
|
ORG;
				$PERFORMING_ORGANIZATIONS[$organization_key] = $org_text;
			}


            /**
             * IF EMPTY FIND PARENT OBSERVATION
             */
			if(empty($rows)){
			    $parent = self::findParentObservation($order_observations, $order_observation);
			    if($parent != false){

                    $parent_row['code'] = $parent[2];

                    $parent_row['code'] = $parent['OBX'][3][1];
                    $parent_row['description'] = $parent['OBX'][3][2];

                    if($observation['OBX'][2] == 'SN'){
                        $parent_row['result'] = $parent['OBX'][5][2];
                    }elseif($observation['OBX'][2] == 'CWE'){
                        $parent_row['result'] = $parent['OBX'][5][2];
                    }else{
                        $parent_row['result'] = $parent['OBX'][5];
                    }


                    $parent_row['uom'] = $parent['OBX'][6][1];
                    $parent_row['range'] = $parent['OBX'][7];
                    $parent_row['abnormal_flag'] = $parent['OBX'][8][0];
                    $parent_row['status'] = $parent['OBX'][11];
                    $parent_row['obs_date'] = $parent['OBX'][14][1];

                    if($parent_row['obs_date'] != ''){
                        $parent_row['obs_date'] = date(self::$dates_format_compact, strtotime($parent_row['obs_date']));
                    }

                    if($parent['NTE'] && is_array($parent['NTE'])){
                        if(is_array($parent['NTE'])){
                            $parent_row['note'] = '';
                            foreach($parent['NTE'] as $nte){
                                $parent_row['note'] .= $nte[3][0] . ' ';
                            }
                        }
                    }else{
                        $parent_row['note'] = '- ';
                    }

                    foreach ($parent_row as $column => $value){
                        $len = strlen($value);
                        if($columns_lens[$column] < $len){
                            $columns_lens[$column] = $len + 1;
                        }
                    }

                    $rows[] = $parent_row;

                }

            }


			$rows[] = $row;

		}

		$code = str_pad('CODE', $columns_lens['code']);
		$description = str_pad('DESCRIPTION', $columns_lens['description']);
		$result = str_pad('RESULT', $columns_lens['result']);
		$uom = str_pad('UOM', $columns_lens['uom']);
		$range = str_pad('RANGE', $columns_lens['range']);
		$abn = str_pad('ABN', $columns_lens['abnormal_flag']);
		$status = str_pad('STATUS', $columns_lens['status']);
		$datetime = str_pad('DATE/TIME', $columns_lens['obs_date']);
		$comment = str_pad('COMMENT', $columns_lens['note']);

		foreach ($columns_lens as $columns_len){
			$line_len += $columns_len;
		}

		$header_line = str_pad('',$line_len, '=');
		$row_line = str_pad('',$line_len, '-');

		$OBSERVATIONS = <<<HEAD

|	|{$header_line}
|       | {$code}| {$description}| {$result}| {$uom}| {$range}| {$abn}| {$status}| {$datetime}| {$comment}
|       |{$header_line}

HEAD;

		foreach ($rows as $row){

			$code = str_pad($row['code'], $columns_lens['code']);
			$description = str_pad($row['description'], $columns_lens['description']);
			$result = str_pad($row['result'], $columns_lens['result']);
			$uom = str_pad($row['uom'], $columns_lens['uom']);
			$range = str_pad($row['range'], $columns_lens['range']);
			$abn = str_pad($row['abnormal_flag'], $columns_lens['abnormal_flag']);
			$status = str_pad($row['status'], $columns_lens['status']);
			$datetime = str_pad($row['obs_date'], $columns_lens['obs_date']);
			$comment = str_pad($row['note'], $columns_lens['note']);

			$OBSERVATIONS .= <<<HEAD
|       | {$code}| {$description}| {$result}| {$uom}| {$range}| {$abn}| {$status}| {$datetime}| {$comment}
|       |{$row_line}

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

		$PERFORMING_ORGANIZATIONS = implode(PHP_EOL, $PERFORMING_ORGANIZATIONS);

		$text = <<<OBSERVATIONS
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       {$observation_number}. ORDER                                                | TEST
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       {$placer_order_no}| TEST NAME: $test_name
|       {$filler_order_no}| TEST REPORT DATE: $test_report_date
|       {$placer_group_no}| TEST REPORT NUMBER: $test_report_number
|                                                               | TEST NOTES: {$report_notes}   
|       {$ordering_provider_id}
|       {$ordering_provider_name}
|       
|
|       OBSERVATIONS:
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       $OBSERVATIONS|
| 
|       SPECIMEN
|       ---------------------------------------------------------------------------------------------------------------------------------------------
|       $SPECIMEN
|
|       PERFORMING ORGANIZATIONS:
|       ---------------------------------------------------------------------------------------------------------------------------------------------
| 
$PERFORMING_ORGANIZATIONS
|

OBSERVATIONS;
		return $text;
	}
}