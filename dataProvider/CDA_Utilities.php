<?php

/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 4/18/17
 * Time: 8:12 PM
 */
class CDA_Utilities {

	/**
	 * @param string $flavor
	 *
	 * @return array
	 */
	protected function nullFlavor($flavor = 'UNK'){
		$buff['@attributes']['nullFlavor'] = $flavor;
		return $buff;
	}

	/**
	 * @param $dates
	 * @param $IVL_TS
	 *
	 * @return mixed
	 */
	protected function effectiveTime($dates, $IVL_TS = true){

		if($IVL_TS){
			$time['@attributes']['xsi:type'] = 'IVL_TS';
			if(!isset($dates)){
				$time['low'] = $this->nullFlavor();
				return  $time;
			}
			if(is_string($dates)){
				$time['low'] = $this->date($dates);
				return  $time;
			}
			if(isset($dates['Low']) && is_string($dates['Low'])){
				$time['low'] = $this->date($dates['Low']);
			}
			if(isset($dates['High']) && is_string($dates['High'])){
				$time['high'] = $this->date($dates['High']);
			}

			return $time;
		}

		if(!isset($dates)){
			$time = $this->nullFlavor();
			return  $time;
		}

		if(is_string($dates)){
			$time = $this->date($dates);
			if(is_string($time)){
				$time = ['@attributes' => ['value' => $time]];
			}
			return  $time;
		}
		if(is_array($dates)){
			$time = $this->date($dates['Low']);
			if(is_string($time)){
				$time = ['@attributes' => ['value' => $time]];
			}
			return  $time;
		}

		return $this->nullFlavor();
	}

	/**
	 * @param $data
	 * @param $type
	 *
	 * @return array
	 */
	protected function value($data, $type = 'CD'){
		$value = [];

		if(!isset($data) || $data == ''){
			$value = $this->nullFlavor();

		}elseif($type == 'CD' || $type == 'CO'){

			$value['@attributes']['xsi:type'] = $type;
			$value['@attributes']['code'] = $data['Code'];
			$value['@attributes']['codeSystem'] = $data['CodeSystem'];
			if(isset($data['DisplayName']) && $data['DisplayName'] != ''){
				$value['@attributes']['displayName'] = $data['DisplayName'];
			}
			if(isset($data['CodeSystemName']) && $data['CodeSystemName'] != ''){
				$value['@attributes']['codeSystemName'] = $data['CodeSystemName'];
			}

		}elseif($type == 'ST'){

			$value['@attributes']['xsi:type'] = $type;
			$value['@value'] = $data;

		}elseif($type == 'IVL_PQ'){

			$value['@attributes']['xsi:type'] = $type;
			if(isset($data['Low']) && $data['Low'] != ''){
				$value['low']['@attributes']['value'] = $data['Low'];
			}else{
				$value['low'] = $this->nullFlavor();
			}
			if(isset($data['High']) && $data['High'] != ''){
				$value['high']['@attributes']['value'] = $data['High'];
			}else{
				$value['high'] = $this->nullFlavor();
			}
		}

		return $value;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	protected function referenceRange($data){
		$tpl['observationRange'] = [];

		if($data == 'UNK'){
			$tpl['observationRange'] = $this->nullFlavor();
			return $tpl;
		}

		if(is_array($data)){
			if(isset($data['Value'])){
				$tpl['observationRange'] = $this->value($data['Value'], 'ST');
			}elseif(isset($data['Low']) || isset($data['High'])){
				$tpl['observationRange'] = $this->value($data, 'IVL_PQ');
			}elseif(isset($data['Code'])){
				$tpl['observationRange'] = $this->value($data, 'CO');
			}
		}elseif(is_string($data)){
			$tpl['observationRange'] = $this->value($data, 'ST');
		}

		return $tpl;

	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	protected function performer($data){

		if(!isset($data) || is_string($data)){
			return $this->nullFlavor();
		}

		$performer = [];

		if(isset($data['NPI']) && $data['NPI'] != ''){
			$performer['id']['@attributes']['root'] = '2.16.840.1.113883.4.873';
			$performer['id']['@attributes']['extension'] = $data['NPI'];
		}else{
			$performer['id']['@attributes']['root'] = '2.16.840.1.113883.19.4';
			$performer['id']['@attributes']['extension'] = $data['Id'];
		}

		$performer['assignedEntity']['addr'] = $this->addr($data['Address']);
		$performer['assignedEntity']['telecom'] = $this->telecom($data['Telecom']);
		$performer['assignedEntity']['representedOrganization'] = $this->representedOrganization($data['Organization']);
		return $performer;
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	protected function telecom($data){
		$number = str_replace(['(', ')', '-', ' '], '', trim($data['Number']));
		if($number != '' && $number != 'UNK'){
			$phone['@attributes'] = [
				'xsi:type' => 'TEL',
				'value' => 'tel:' . $number
			];
			if(isset($data['Use']) && $data['Use'] != 'UNK'){
				$phone['@attributes']['use'] = $data['Use'];
			}
		}else{
			$phone['@attributes']['nullFlavor'] = 'UNK';
		}

		return $phone;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	protected function addr($data){

		$addr = [];

		if(isset($data['Use']) !== false){
			$addr['@attributes']['use'] = $data['Use'];
		}

		if($data['StreetAddressLine'] == 'UNK' || $data['StreetAddressLine'] == ''){
			$addr['streetAddressLine'] = $this->nullFlavor('NI');
		}else{
			$addr['streetAddressLine']['@value'] = $data['StreetAddressLine'];
		}

		if($data['City'] == 'UNK' || $data['City'] == ''){
			$addr['city'] = $this->nullFlavor();
		}else{
			$addr['city']['@value'] = $data['City'];
		}

		if($data['State'] == 'UNK' || $data['State'] == ''){
			$addr['state'] = $this->nullFlavor();
		}else{
			$addr['state']['@value'] = $data['State'];
		}

		if($data['PostalCode'] == 'UNK' || $data['PostalCode'] == ''){
			$addr['postalCode'] = $this->nullFlavor();
		}else{
			$addr['postalCode']['@value'] = $data['PostalCode'];
		}

		if($data['Country'] == 'UNK' || $data['Country'] == ''){
			$addr['country'] = $this->nullFlavor();
		}else{
			$addr['country']['@value'] = $data['Country'];
		}

		return $addr;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	protected function representedOrganization($data){
		$org = [];

		if(isset($data['NPI']) && $data['NPI'] != ''){
			$org['id']['@attributes']['root'] = '2.16.840.1.113883.4.873';
			$org['id']['@attributes']['extension'] = $data['NPI'];
		}else{
			$org['id']['@attributes']['root'] = '2.16.840.1.113883.19.4';
			$org['id']['@attributes']['extension'] = $data['Id'];
		}

		$org['name'] = $data['Name'];
		$org['telecom'] = $this->telecom($data['Telecom']);
		$org['addr'] = $data['Address'];

		return $org;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	protected function manufacturedProduct($data){
		$product = [];




		return $product;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function referenceRangeToText($data){

		if(is_array($data)){
			$ranges = [];

			if(isset($data['Value']) && $data['Value'] != ''){
				$ranges[] = $data['Value'];
			}
			if(isset($data['Low']) && $data['Low'] != ''){
				$ranges[] = $data['Low'];
			}
			if(isset($data['High']) && $data['High'] != ''){
				$ranges[] = $data['High'];
			}
			return implode(' - ', $ranges);
		}

		return $data;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function organizationToText($data){
		$text = '';
		if(isset($data['Name'])){
			$text .= $data['Name'];
		}
		if(isset($data['Address'])){
			$text .= ' - Address: ' . $this->addrToText($data['Address']);
		}
		if(isset($data['Telecom']) && isset($data['Telecom']['Number'])){
			$text .= ' - Tel: ' . $data['Telecom']['Number'];
		}
		return $text;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function diagnosisToText($data){
		$texts = [];

		foreach($data as $dx){
			$text = '';
			if(isset($dx['Code'])){
				$text .= $this->codeToText($dx['Code']);
			}
			if(isset($dx['Status']) && $dx['Status'] != ''){
				$text .= ' (' . $dx['Status'] . ')';
			}
			$texts[] = $text;
		}
		return implode(' - ', $texts);
	}
	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function addrToText($data){
		$text = '';
		if(isset($data['StreetAddressLine'])){
			$text .= trim($data['StreetAddressLine']);
		}
		if(isset($data['City'])){
			$text .= ', ' . trim($data['City']);
		}
		if(isset($data['State'])){
			$text .= ', ' . trim($data['State']);
		}
		if(isset($data['PostalCode'])){
			$text .= ' ' . trim($data['PostalCode']);
		}
		if(isset($data['Country'])){
			$text .= ' ' . $data['Country'];
		}
		return $text;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function name($data){
		$name = [];

		if(isset($data['Title']) && $data['Title'] != ''){
			$name['prefix'] = $data['Title'];
		}

		if(isset($data['Given']) && $data['Given'] != ''){
			$name['given'][] = $data['Given'];
		}

		if(isset($data['Middle']) && $data['Middle'] != ''){
			$name['given'][] = [
				'@attributes' => ['qualifier' => 'IN'],
				'@value' => $data['Middle']
			];
		}

		if(isset($data['Family']) && $data['Family'] != ''){
			$name['family'] = $data['Family'];
		}

		if(empty($name)){
			return $this->nullFlavor();
		}

		return $name;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function reference($value){
		$buff['@attributes']['value'] = $value;
		return $buff;
	}

	/**
	 * @param $date
	 *
	 * @return mixed
	 */
	public function date($date){
		$time = strtotime($date);
		if($time === false){
			return $this->nullFlavor();
		}
		return date('Ymd', $time);
	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	protected function code($data){

		if($data == 'UNK'){
			return $this->nullFlavor();
		}

		$tpl['@attributes']['code'] = $data['Code'];
		$tpl['@attributes']['codeSystem'] = $data['CodeSystem'];
		if(isset($data['CodeSystemName'])){
			$tpl['@attributes']['codeSystemName'] = $data['CodeSystemName'];
		}
		if(isset($data['DisplayName'])) {
			$tpl['@attributes']['displayName'] = $data['DisplayName'];
		}

		return $tpl;
	}

	/**
	 * @param               $date
	 * @param string|array  $format
	 *
	 * @return false|string
	 */
	protected function dateToText($date, $format = 'F j, Y'){

		if(is_array($date)){
			$dates = [];

			if(isset($date['Low'])){
				$dates[] = 'From ' . date($format, strtotime($date['Low']));
			}
			if(isset($date['High'])){
				$dates[] = 'To ' . date($format, strtotime($date['High']));
			}

			return implode(' - ', $dates);
		}

		return date($format, strtotime($date));
	}

	/**
	 * @param $code
	 *
	 * @return string
	 */
	protected function codeToText($code){
		$texts = [];
		if(isset($code['DisplayName'])) $texts[] = $code['DisplayName'];
		if(isset($code['CodeSystemName'])) $texts[] = $code['CodeSystemName'];
		if(isset($code['Code'])) $texts[] = $code['Code'];
		return implode(' - ', $texts);
	}

	/**
	 * @param $code
	 *
	 * @return mixed
	 */
	protected function statusToText($code){
		if(is_string($code)){
			return $code;
		}
		return $code['DisplayName'];
	}

	/**
	 * @param $date
	 *
	 * @return bool
	 */
	protected function isActiveByDate($date){
		if(!isset($date)){
			return true;
		}

		if($date == '0000-00-00' || $date == '0000-00-00 00:00:00'){
			return true;
		}

		$date = strtotime($date);

		if($date === false){
			return true;
		}

		$now = time();
		return $date > $now;
	}

	/**
	 * @param $string
	 *
	 * @return mixed|string
	 */
	public function clean($string){
		// Pass 1
		$cleanIt = html_entity_decode($string);
		// Pass 2
		$cleanIt = strip_tags($cleanIt);
		// Pass 3
		$cleanIt = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $cleanIt);
		// Pass 4
		$cleanIt = preg_replace("/\p{Cc}+/u", "", $cleanIt);
		return $cleanIt;
	}

	/**
	 * @param $columns
	 * @param $rows
	 * @param $title
	 * @param $vertical
	 *
	 * @return array
	 */
	protected function createTable($columns, $rows, $title = null, $vertical = false){

		$table = [];
		$table['@attributes']['border'] = '1';
		$table['@attributes']['width'] = '100%';

		if($title){
			$table['caption'] = $title;
		}

		if($vertical){
			$x_row_map = [];

			$i = 0;
			foreach($columns as $index => $column){
				if($index == 'DT'){
					$table['thead']['tr'][$i]['th'][] = $column ;
				}else{
					$table['tr'][$i]['th'][] = $column ;
				}
				$x_row_map[$index] = $i;
				$i++;
			}

			foreach($rows as $row){
				foreach($row as $index => $value){
					$x_row_index = $x_row_map[$index];
					if($index == 'DT'){
						$table['thead']['tr'][$x_row_index]['th'][] = $value;
					}else{
						$table['tr'][$x_row_index]['td'][] = $value == '' ? '-' : $value;
					}
					$stop = false;
				}
			}

			$stop = false;

		}else{

			foreach($columns as $index => $column){
				$table['thead']['tr']['th'][$index] = $column;
			}

			if(empty($rows)) return $table;

			foreach($rows as $row_id => $row){

				if(empty($row)) continue;

				$tr = [];
				if(is_string($row_id)){
					$tr['@attributes']['ID'] = $row_id;
				}

				foreach($row as $value_id => $value){
					$td = [];
					if(is_string($value_id)){
						$td['@attributes']['ID'] = $value_id;
					}
					$td['@value'] = $value;
					$tr['td'][] = $td;
				}

				$table['tbody']['tr'][] = $tr;
			}

		}




		return $table;
	}

	/**
	 * @param $texts
	 *
	 * @return array
	 */
	protected function createParagraphs($texts){
		$paragraphs = [];
		foreach($texts as $text_id => $text){
			$p = [];
			if(is_string($text_id)){
				$p['@attributes']['ID'] = $text_id;
				$p['@value'] = $text;
			}else{
				$p = $text;
			}
			$paragraphs[] = $p;
		}

		return $paragraphs;
	}

	/**
	 * @param $displayName
	 *
	 * @return string
	 */
	protected function codeSystem($displayName){

		switch($displayName){
			case 'CPT':
				return '2.16.840.1.113883.6.12';
			case 'CPT4':
			case 'CPT-4':
				return '2.16.840.1.113883.6.12';
			case 'ICD9':
			case 'ICD-9':
				return '2.16.840.1.113883.6.42';
			case 'ICD10':
			case 'ICD-10':
			case 'ICD10-CM':
				return '2.16.840.1.113883.6.3';
			case 'LN':
			case 'LOINC':
				return '2.16.840.1.113883.6.1';
			case 'NDC':
				return '2.16.840.1.113883.6.6';
			case 'RXNORM':
				return '2.16.840.1.113883.6.88';
			case 'SNOMED':
			case 'SNOMEDCT':
			case 'SNOMED-CT':
				return '2.16.840.1.113883.6.96';
			case 'NPI':
				return '2.16.840.1.113883.4.6';
			case 'UNII':
				return '2.16.840.1.113883.4.9';
			case 'NCI':
				return '2.16.840.1.113883.3.26.1.1';
			case 'ActPriority':
				return '2.16.840.1.113883.1.11.16866';
			case 'TAXONOMY':
				return '2.16.840.1.114222.4.11.106';
			case 'CDCREC':
			case 'PH_RaceAndEthnicity_CDC':
				return '2.16.840.1.113883.6.238';
			default:
				return '';
		}
	}

}