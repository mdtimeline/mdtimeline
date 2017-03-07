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
    static $buffer = [];

    /**
     * @var array
     */
    static $children_reports = [];

	static function printMessage($message, $title = ''){

        self::$message = $message;
        self::$buffer = [];

		if($title != ''){
			$title = strtoupper($title);
			self::$buffer['TITLE'] = $title;
		}

		foreach ($message->data as $key => $data){
			if($key == 'MSH'){
				self::printMSH($data);
			}elseif($key == 'PATIENT_RESULT'){
				self::printPatientResults($data);
			}
		}

		self::$buffer['FOOTER'] = "{$title} END!";

		return self::doPrint();

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

		$LINE_ONE = ' ' . $MSG_TYPE;
		$LINE_TWO = " $DATE| $CONTROL_ID";
		$LINE_THREE = " $SENDING_APP| $RECEIVING_APP";
		$LINE_FOUR = " $SENDING_FACILITY| $RECEIVING_FACILITY";

		self::$buffer['SECTIONS'][] = [
			'TITLE' => ' HL7 MSG HEADER',
			'ROWS' => [
				$LINE_ONE,
				$LINE_TWO,
				$LINE_THREE,
				$LINE_FOUR
			]
		];
	}

	private static function printPID($data){

	}

	private static function printPatientResults($patient_results){

		$SECTION['TITLE'] = ' RESULTS';

		foreach ($patient_results as $index => $patient_result){
			$SECTION['SECTIONS'][] = self::printPatientResult($patient_result, $index + 1);
		}

		self::$buffer['SECTIONS'][] = $SECTION;

		return;
	}

	private static function printPatientResult($patient_result, $result_number){

		$SECTION['TITLE'] = 'RESULT';
		$SECTION['SUB_SECTIONS'][] = self::printPatient($patient_result['PATIENT']);
		$SECTION['SUB_SECTIONS'][] = self::printOrderObservations($patient_result['ORDER_OBSERVATION']);

		return $SECTION;
	}

	private static function printPatient($patient)
	{

		$SECTION['TITLE'] = 'PATIENT';

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


		$SECTION['ROWS'][] =  "{$patient_name}| {$patient_dis}";
		$SECTION['ROWS'][] =  "{$patient_sex}| {$patient_race}";
		$SECTION['ROWS'][] =  "{$patient_dob}| {$patient_lang}";

		return $SECTION;
	}

	private static function printOrderObservations($order_observations)
	{

		$SECTION['TITLE'] = 'REPORTS';

		$REPORTS = [];
		$obx_report_map = [];
		$report_number_to_key = [];

		foreach ($order_observations as $index => $order_observation){

			$order_no = $order_observation['OBR'][2][1];
			$report_no = $order_observation['OBR'][3][1];
			$report_loinc = $order_observation['OBR'][4][1];
			$parent_order_no =  $order_observation['OBR'][28][0][2][1];
			$parent_report_no =  $order_observation['OBR'][29][2][1];
			$report_key = $report_no .'-'.$report_loinc;

			$parent_id = $order_observation['OBR'][26][1][1];
			$parent_sub_id = $order_observation['OBR'][26][2];
			$parent_obx_key = $parent_id .'-'. $parent_sub_id;

			if($parent_id != '' && $parent_sub_id != '' && isset($obx_report_map[$parent_obx_key])){
				self::childrenObservationsHandler($REPORTS[$obx_report_map[$parent_obx_key]], $order_observation, $index);
			}else{
				$REPORTS[$report_key] = self::observationsHandler($order_observation, $index, $obx_report_map, $report_key);
			}
		}

		$SECTION['SECTIONS'] = $REPORTS;

		return $SECTION;

	}

	private static function observationsHandler($order_observation, $index, &$obx_report_map, $report_key)
	{

		$REPORT = [];
		$REPORT['TITLE'] = 'REPORT';

		$performing_organizations = [];

		if ($order_observation['OBR'][4][2] != '') {
			$test_name = $order_observation['OBR'][4][2];
		} else {
			$test_name = $order_observation['OBR'][4][1];
		}
		$test_report_number = $order_observation['OBR'][3][1];
		$test_report_date = $order_observation['OBR'][22][1];
		if ($test_report_date != '') {
			$test_report_date = date(self::$dates_format, strtotime($test_report_date));
		}

		$placer_order_no = 'PLACER ORDER NO.: ' . $order_observation['OBR'][2][1];
		$filler_order_no = 'FILLER ORDER NO.: ' . $order_observation['ORC'][3][1];
		$placer_group_no = 'PLACER GROUP NO.: ' . $order_observation['ORC'][4][1];

		$REPORT['ROWS'][] = "$placer_order_no";
		$REPORT['ROWS'][] = "$filler_order_no";
		$REPORT['ROWS'][] = "$placer_group_no";
		$REPORT['ROWS'][] = '';

		$ordering_provider_id = $order_observation['ORC'][12][0][9][1] . ' - ' . $order_observation['ORC'][12][0][9][3];
		$ordering_provider_name = '';
		// prefix
		if ($order_observation['ORC'][12][0][6] != '') {
			$ordering_provider_name .= $order_observation['ORC'][12][0][6] . ' ';
		}
		// last
		$ordering_provider_name .= $order_observation['ORC'][12][0][2][1] . ', ';
		// first
		if ($order_observation['ORC'][12][0][3] != '') {
			$ordering_provider_name .= $order_observation['ORC'][12][0][3] . ' ';
		}
		// middle
		if ($order_observation['ORC'][12][0][4] != '') {
			$ordering_provider_name .= $order_observation['ORC'][12][0][4] . ' ';
		}
		// suffix
		if ($order_observation['ORC'][12][0][5] != '') {
			$ordering_provider_name .= $order_observation['ORC'][12][0][5] . ' ';
		}
		$ordering_provider_id = 'PROVIDER ID: ' . $ordering_provider_id;
		$ordering_provider_name = 'PROVIDER NAME: ' . $ordering_provider_name;

		$REPORT['ROWS'][] = $ordering_provider_id;
		$REPORT['ROWS'][] = $ordering_provider_name;
		$REPORT['ROWS'][] = '';

		$test_name = "TEST NAME: $test_name";
		$test_report_date = "TEST REPORT DATE: $test_report_date";
		$test_report_number = "TEST REPORT NUMBER: $test_report_number";

		$REPORT['ROWS'][] = "$test_name";
		$REPORT['ROWS'][] = "$test_report_date";
		$REPORT['ROWS'][] = "$test_report_number";
		$REPORT['ROWS'][] = '';


		$NOTES['TITLE'] = 'NOTES:';
		if (isset($order_observation['NTE']) && is_array($order_observation['NTE'])) {
			foreach ($order_observation['NTE'] as $i => $note) {
				$NOTES['ROWS'][] = $index + 1 . '. ' . $note[3][0];
			}
		}

		if (!empty($NOTES['ROWS'])) {
			$NOTES['ROWS'][] = '';
			$REPORT['SUB_SECTIONS'][] = $NOTES;
		}

		$TABLE = [];
//			$TABLE['TITLE'] = 'OBSERVATIONS';
		$TABLE['TH']['CODE'] = 'CODE';
		$TABLE['TH']['DESCRIPTION'] = 'DESCRIPTION';
		$TABLE['TH']['RESULT'] = 'RESULT';
		$TABLE['TH']['UOM'] = 'UOM';
		$TABLE['TH']['RANGE'] = 'RANGE';
		$TABLE['TH']['ABN'] = 'ABN';
		$TABLE['TH']['STATUS'] = 'STATUS';
		$TABLE['TH']['DATE'] = 'DATE';
		$TABLE['TH']['COMMENT'] = 'COMMENT';

		foreach ($order_observation['OBSERVATION'] as $observation) {

			if (!isset($observation['OBX'])) continue;

			$TR['CODE'] = $observation['OBX'][3][1];
			$TR['DESCRIPTION'] = $observation['OBX'][3][2];

			if ($observation['OBX'][2] == 'SN') {
				$TR['RESULT'] = $observation['OBX'][5][2];
			} elseif ($observation['OBX'][2] == 'CWE') {
				$TR['RESULT'] = $observation['OBX'][5][2];
			} else {
				$TR['RESULT'] = $observation['OBX'][5];
			}

			$TR['UOM'] = $observation['OBX'][6][1];
			$TR['RANGE'] = $observation['OBX'][7];
			$TR['ABN'] = $observation['OBX'][8][0];
			$TR['STATUS'] = $observation['OBX'][11];
			$TR['DATE'] = $observation['OBX'][14][1];

			if ($TR['DATE'] != '') {
				$TR['DATE'] = date(self::$dates_format_compact, strtotime($TR['DATE']));
			}

			if ($observation['NTE'] && is_array($observation['NTE'])) {
				if (is_array($observation['NTE'])) {
					$TR['COMMENT'] = '';
					foreach ($observation['NTE'] as $nte) {
						$TR['COMMENT'] .= $nte[3][0] . ' ';
					}
				}
			} else {
				$TR['COMMENT'] = '-';
			}

			$id = $observation['OBX'][3][1];
			$sub_id = $observation['OBX'][4];

			if($id === '' || $sub_id === ''){
				$TABLE['TR'][] = $TR;
			}else{
				$TABLE['TR'][$id.'-'.$sub_id] = $TR;
				$obx_report_map[$id.'-'.$sub_id] = $report_key;
			}

			/**
			 * ORGANIZATION
			 */
			$organization_key = $observation['OBX'][23][1] != '' ? str_replace(' ', '_', $observation['OBX'][23][1]) : 'UNKNOWN';

			if (!array_key_exists($organization_key, $performing_organizations)) {
				$organization_name = $observation['OBX'][23][1];
				$organization_address = $observation['OBX'][24][1][1];
				$organization_city = $observation['OBX'][24][3];
				$organization_state = $observation['OBX'][24][4];
				$organization_zip = $observation['OBX'][24][5];
				$organization_country = $observation['OBX'][24][6];
				$organization_parish = $observation['OBX'][24][9];

				$performing_organization_director_id = $observation['OBX'][25][1];
				$performing_organization_director_name = '';

				if ($observation['OBX'][25][6] != '') {
					$performing_organization_director_name .= $observation['OBX'][25][6] . ' ';
				}

				if ($observation['OBX'][25][2][1] != '') {
					$performing_organization_director_name .= $observation['OBX'][25][2][1] . ' ';
				}

				if ($observation['OBX'][25][3] != '') {
					$performing_organization_director_name .= $observation['OBX'][25][3] . ' ';
				}

				if ($observation['OBX'][25][4] != '') {
					$performing_organization_director_name .= $observation['OBX'][25][4] . ' ';
				}


				$org['ROWS'][] = "CONTACT: $performing_organization_director_name";
				$org['ROWS'][] = "NAME: {$organization_name}";
				$org['ROWS'][] = "STREET: {$organization_address}";
				$org['ROWS'][] = "CITY: {$organization_city}";
				$org['ROWS'][] = "SATE: {$organization_state}";
				$org['ROWS'][] = "ZIP CODE: {$organization_zip}";
				$org['ROWS'][] = "COUNTRY: {$organization_country}";
				$org['ROWS'][] = "COUNTY/PARISH CODE: {$organization_parish}";
				$org['ROWS'][] = '';
				$performing_organizations[$organization_key] = $org;

			}
		}

		$OBSERVATIONS['TITLE'] = 'OBSERVATIONS:';
		$OBSERVATIONS['TABLES'][] = $TABLE;

		$REPORT['SUB_SECTIONS'][] = $OBSERVATIONS;

		/**
		 * PERFORMING ORGANIZATIONS
		 */
		if(!empty($performing_organizations)){

			$PERFORMING_ORGANIZATION['TITLE'] = 'PERFORMING ORGANIZATIONS:';
			foreach ($performing_organizations as $performing_organization){
				foreach ($performing_organization['ROWS'] as $org_row){
					$PERFORMING_ORGANIZATION['ROWS'][] = $org_row;
				}
			}
			$REPORT['SUB_SECTIONS'][] = $PERFORMING_ORGANIZATION;
		}

		/**
		 * SPECIMEN
		 */
		if (isset($order_observation['SPECIMEN']) && isset($order_observation['SPECIMEN']['SPM'])) {

			$type = $order_observation['SPECIMEN']['SPM'][4][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][4][2];

			$collection_start = $order_observation['SPECIMEN']['SPM'][17][1][1];
			$collection_stop = $order_observation['SPECIMEN']['SPM'][17][2][1];

			if ($collection_start != '') {
				$collection_start = date(self::$dates_format, strtotime($collection_start));
			} else {
				$collection_start = 'UNKNOWN';
			}

			if ($collection_stop != '') {
				$collection_stop = date(self::$dates_format, strtotime($collection_stop));
			} else {
				$collection_stop = 'UNKNOWN';
			}

			$reject_reason = $order_observation['SPECIMEN']['SPM'][21][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][21][2];
			$appropriateness = $order_observation['SPECIMEN']['SPM'][23][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][23][2];
			$condition = $order_observation['SPECIMEN']['SPM'][24][1] . ' - ' . $order_observation['SPECIMEN']['SPM'][24][2];
			$notes = $order_observation['SPECIMEN']['SPM'][21][3];

			$SPECIMEN['TITLE'] = 'SPECIMEN:';
			$SPECIMEN['ROWS'][] = "TYPE: {$type}";
			$SPECIMEN['ROWS'][] = "COLLECTION START DATE: {$collection_start}";
			$SPECIMEN['ROWS'][] = "COLLECTION STOP DATE: {$collection_stop}";
			$SPECIMEN['ROWS'][] = "REJECT REASON: {$reject_reason}";
			$SPECIMEN['ROWS'][] = "APPROPRIATENESS: {$appropriateness}";
			$SPECIMEN['ROWS'][] = "CONDITION: {$condition}";
			$SPECIMEN['ROWS'][] = "NOTES: {$notes}";
			$SPECIMEN['ROWS'][] = '';
			$REPORT['SUB_SECTIONS'][] = $SPECIMEN;
		}

		return $REPORT;
	}

	private static function childrenObservationsHandler(&$PARENT_REPORT, $order_observation, $index){

		$parent_section = false;
		$parent_table = false;
		$parent_row_found = false;


		$TABLE = [];
//			$TABLE['TITLE'] = 'OBSERVATIONS';
		$TABLE['TH']['CODE'] = 'CODE';
		$TABLE['TH']['DESCRIPTION'] = 'DESCRIPTION';
		$TABLE['TH']['RESULT'] = 'RESULT';
		$TABLE['TH']['UOM'] = 'UOM';
		$TABLE['TH']['RANGE'] = 'RANGE';
		$TABLE['TH']['ABN'] = 'ABN';
		$TABLE['TH']['STATUS'] = 'STATUS';
		$TABLE['TH']['DATE'] = 'DATE';
		$TABLE['TH']['COMMENT'] = 'COMMENT';

		foreach ($order_observation['OBSERVATION'] as $observation){

			if(!isset($observation['OBX'])) continue;

			if($parent_row_found === false){
				$parent_id = $order_observation['OBR'][26][1][1];
				$parent_sub_id = $order_observation['OBR'][26][2];

				$parent_key = $parent_id . '-' . $parent_sub_id;

				foreach ($PARENT_REPORT['SUB_SECTIONS'] as &$SUB_SECTION){
					if(!isset($SUB_SECTION['TABLES'])) continue;

					foreach ($SUB_SECTION['TABLES'] as &$SUB_TABLE){
						if(!isset($SUB_TABLE['TR'])) continue;

						foreach ($SUB_TABLE['TR'] as $key => $row ){
							if($parent_key != $key) continue;
							$TABLE['TR'][$key] = $row;
							$parent_section = &$SUB_SECTION;
							$parent_table = &$SUB_TABLE;
							$parent_row_found = true;

							// delete parent row
							unset($SUB_TABLE['TR'][$key]);

						}
					}
				}
			}

			$TR['CODE'] = $observation['OBX'][3][1];
			$TR['DESCRIPTION'] = $observation['OBX'][3][2];

			if($observation['OBX'][2] == 'SN'){
				$TR['RESULT'] = $observation['OBX'][5][2];
			}elseif($observation['OBX'][2] == 'CWE'){
				$TR['RESULT'] = $observation['OBX'][5][2];
			}else{
				$TR['RESULT'] = $observation['OBX'][5];
			}

			$TR['UOM'] = $observation['OBX'][6][1];
			$TR['RANGE'] = $observation['OBX'][7];
			$TR['ABN'] = $observation['OBX'][8][0];
			$TR['STATUS'] = $observation['OBX'][11];
			$TR['DATE'] = $observation['OBX'][14][1];

			if($TR['DATE'] != ''){
				$TR['DATE'] = date(self::$dates_format_compact, strtotime($TR['DATE']));
			}

			if($observation['NTE'] && is_array($observation['NTE'])){
				if(is_array($observation['NTE'])){
					$TR['COMMENT'] = '';
					foreach($observation['NTE'] as $nte){
						$TR['COMMENT'] .= $nte[3][0] . ' ';
					}
				}
			}else{
				$TR['COMMENT'] = '-';
			}

			$id = $observation['OBX'][3][1];
			$sub_id = $observation['OBX'][4];

			$TABLE['TR'][$id . '-' . $sub_id] = $TR;
		}

		if(is_array($parent_section) && empty($parent_table['TR'])){

			$parent_table_index = array_search($parent_table, $parent_section['TABLES']);

			if($parent_table_index !== false){
				unset($parent_section['TABLES'][$parent_table_index]);
			}
		}


		$parent_section['TABLES'][] = $TABLE;

	}


	static function doPrint(){

		$text = '';

		$buffer = self::$buffer;

		if(isset($buffer['TITLE'])){
			$text .= <<<TITLE
|------------------------------------------------------------------------------------------------------------------------------------------------------
| {$buffer['TITLE']}
|------------------------------------------------------------------------------------------------------------------------------------------------------
|

TITLE;
		}

		if(isset($buffer['SECTIONS'])){
			$text .= self::sectionsHandler($buffer['SECTIONS']);
		}

		if(isset($buffer['FOOTER'])){
			$text .= <<<FOOTER
|
|------------------------------------------------------------------------------------------------------------------------------------------------------
| {$buffer['FOOTER']}
|------------------------------------------------------------------------------------------------------------------------------------------------------

FOOTER;
		}

		return $text;

	}

	static function sectionsHandler($SECTIONS, $indent = 0, $is_sub = false){

		$buffer = '';
		$pad = str_pad('', $indent * 3);
		$line = str_pad("{$pad}",150, '-');

		foreach ($SECTIONS as $SECTION) {


			if(isset($SECTION['TITLE'])){
				if($is_sub){
					$buffer .= <<<SECTION
|{$pad}{$SECTION['TITLE']}
|{$line}

SECTION;
				} else{
					$buffer .= <<<SECTION
|{$line}
|{$pad}{$SECTION['TITLE']}
|{$line}

SECTION;
				}
			}


			if(isset($SECTION['ROWS'])){
				foreach ($SECTION['ROWS'] as $ROW) {
					$buffer .= <<<ROWS
|{$pad}{$ROW}

ROWS;
				}
			}


			if (isset($SECTION['TABLES'])) {
				$buffer .= self::tablesHandler($SECTION['TABLES'], $indent);
			}

			if (isset($SECTION['SUB_SECTIONS'])) {
				$buffer .= self::sectionsHandler($SECTION['SUB_SECTIONS'], $indent, true);
			}

			if (isset($SECTION['SECTIONS'])) {
				$buffer .= self::sectionsHandler($SECTION['SECTIONS'], $indent + 1, false);
			}
		}

		return $buffer;

	}

	static function tablesHandler($TABLES, $indent){


		$buffer = '';
		$pad = str_pad('', $indent * 3);

		foreach ($TABLES as $TABLE){
			$buffer .= self::tableHandler($TABLE, $pad);
		}

		return $buffer;
	}

	static function tableHandler($TABLE, $pad){

		$buffer = '';
		$line_len = 0;
		$columns_lens = [];
		$rows = [];

		if(isset($TABLE['TH'])){
			foreach ($TABLE['TH'] as $COL => $VAL){
				$columns_lens[$COL] = strlen($VAL);
			}
		}

		if(isset($TABLE['TR'])) {
			foreach ($TABLE['TR'] as $ROW => $COLS) {
				foreach ($COLS as $COL => $VAL){
					$len = strlen($VAL);
					if(!isset($columns_lens[$COL]) || $columns_lens[$COL] < $len){
						$columns_lens[$COL] = $len;
					}
				}
			}
		}

		$line_len = 0;
		foreach ($columns_lens as $columns_len){
			$line_len = $line_len + ($columns_len +  5);
		}

		$th_line = str_pad("|$pad|", $line_len, '=') . PHP_EOL;
		$tr_line = str_pad("|$pad|", $line_len, '-') . PHP_EOL;

		$buffer .= '|' . PHP_EOL;

		if(isset($TABLE['TH'])){

			$buffer .= $th_line;

			$TH = '';

			foreach ($TABLE['TH'] as $COL => $VAL){
				$col_len = $columns_lens[$COL] + 4;
				$TH .= str_pad("| $VAL ", $col_len);
			}

			$buffer .= <<<TH
|{$pad}{$TH}

TH;
			$buffer .= $th_line;
		}

		if(isset($TABLE['TR'])) {
			foreach ($TABLE['TR'] as $ROW => $COLS) {

				$TR = '';
				foreach ($COLS as $COL => $VAL){
					$col_len = $columns_lens[$COL] + 4;
					$TR .= str_pad("| $VAL ", $col_len);
				}

				$buffer .= <<<TR
|{$pad}{$TR}

TR;
				$buffer .= $tr_line;
			}
		}

		$buffer .= '|' . PHP_EOL;


		return $buffer;
	}

}