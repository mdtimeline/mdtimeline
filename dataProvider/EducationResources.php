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
include_once (ROOT . '/classes/XML2Array.php');

class EducationResources {

	/**
	 * @var MatchaCUP
	 */
	private $e;
	/**
	 * @var MatchaCUP
	 */
	private $pe;

	/**
	 * @var string
	 */
	private $search_url = 'https://wsearch.nlm.nih.gov/ws/query';

	/**
	 * @var string
	 */
	private $medline_connect_url = 'https://apps.nlm.nih.gov/medlineplus/services/mpconnect_service.cfm';

	/**
	 * @var Medications
	 */
	private $Medications;

	/**
	 * @var Orders
	 */

	private $Orders;
	/**
	 * @var ActiveProblems
	 */
	private $ActiveProblems;

	function __construct() {

		$this->e = MatchaModel::setSenchaModel('App.model.administration.EducationResource');
		$this->pe = MatchaModel::setSenchaModel('App.model.patient.EducationResource', true);
	}

	public function search($params){

		$params = (array) $params;

		$search_url = $this->search_url . '?' . http_build_query($params);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT => 2,
			CURLOPT_TIMEOUT => 2,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $search_url
		));
		$data = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_error = curl_error($curl);
		curl_close($curl);


		if ($curl_errno > 0) {
			return [
				'success' =>false,
				'error' => $curl_error
			];
		} else {

			$array = XML2Array::createArray($data);

			$results = [
				'success' => true,
				'total' => 0,
				'data' => []
			];

			if(!isset($array['nlmSearchResult']['count']) || !isset($array['nlmSearchResult']['list'])){
				return $results;
			}

			$documents = isset($array['nlmSearchResult']['list']['document'][0]) ?
				$array['nlmSearchResult']['list']['document'] : [ $array['nlmSearchResult']['list']['document'] ];

			foreach ($documents as $document) {
				$buff = [
					'rank' => $document['@attributes']['rank'],
					'url' => $document['@attributes']['url']
				];
				foreach ($document['content'] as $content){
					$atr = $content['@attributes']['name'];
					if(!isset($buff[$atr])){
						$buff[$atr] = html_entity_decode($content['@value']);
					}
				}
				$results['data'][] = $buff;

			}

			$results['total'] = $array['nlmSearchResult']['count'];

			return $results;
		}

	}

	public function findEncounterEducationResources($params){

		include_once (ROOT . '/dataProvider/Medications.php');
		include_once (ROOT . '/dataProvider/Orders.php');
		include_once (ROOT . '/dataProvider/ActiveProblems.php');

		if(!isset($this->Medications)){
			$this->Medications = new Medications();
		}
		if(!isset($this->Orders)){
			$this->Orders = new Orders();
		}
		if(!isset($this->ActiveProblems)){
			$this->ActiveProblems = new ActiveProblems();
		}

		$response = [];
		$codes['MEDS'] = $this->Medications->getPatientMedicationsByEid($params->eid);
		$codes['LABS'] = $this->Orders->getPatientLabOrdersByEid($params->eid);
		$codes['PROB'] = $this->ActiveProblems->getPatientActiveProblemByEid($params->eid);

		$codes_searched = [
			'RXNORM' => [],
			'LOINC' => [],
			'SNOMEDCT' => []
		];

		$documents_found = [];

		foreach ($codes['MEDS'] as $med){

			if(array_search($med['RXCUI'], $codes_searched['RXNORM']) !== false) continue;

			$documents = $this->getDocumentsByCodeAndCodeType($med['RXCUI'], 'RXNORM', $documents_found, $params->language);
			$codes_searched['RXNORM'][] = $med['RXCUI'];

			$response = array_merge($response, $documents);
		}
		unset($this->Medications,$codes['MEDS'], $med);

		foreach ($codes['LABS'] as $lab){

			if(array_search($lab['code'], $codes_searched['LOINC']) !== false) continue;

			$documents = $this->getDocumentsByCodeAndCodeType($lab['code'], 'LOINC', $documents_found, $params->language);
			$codes_searched['LOINC'][] = $lab['code'];

			$response = array_merge($response, $documents);
		}
		unset($this->Orders,$codes['LABS'], $med);

		foreach ($codes['PROB'] as $prob){

			if(array_search($prob['code'], $codes_searched['SNOMEDCT']) !== false) continue;

			$documents = $this->getDocumentsByCodeAndCodeType($prob['code'], 'SNOMEDCT', $documents_found, $params->language);
			$codes_searched['SNOMEDCT'][] = $prob['code'];

			$response = array_merge($response, $documents);
		}
		unset($this->ActiveProblems,$codes['PROB'], $prob);

		return $response;

	}

	private function getDocumentsByCodeAndCodeType($code, $code_type, &$documents_found, $language = 'en'){

		if($code_type == 'RXNORM'){
			$mainSearchCriteria = '2.16.840.1.113883.6.88';
		}elseif($code_type == 'LOINC'){
			$mainSearchCriteria = '2.16.840.1.113883.6.1';
		}elseif($code_type == ' ICD10'){
			$mainSearchCriteria = '2.16.840.1.113883.6.90';
		}elseif($code_type == 'ICD9'){
			$mainSearchCriteria = '2.16.840.1.113883.6.103';
		}elseif($code_type == 'SNOMEDCT'){
			$mainSearchCriteria = '2.16.840.1.113883.6.96';
		}else{
			$mainSearchCriteria = '2.16.840.1.113883.6.96';
		}

		$params['mainSearchCriteria.v.cs'] = $mainSearchCriteria;
		$params['mainSearchCriteria.v.c'] = $code;
		$params['informationRecipient.languageCode.c'] = $language;
		$params['knowledgeResponseType'] = 'application/json';

		$search_url = $this->medline_connect_url . '?' . http_build_query($params);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT => 2,
			CURLOPT_TIMEOUT => 2,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $search_url
		));
		$response = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($curl_errno > 0) {
			return [];
		}

		$documents = [];

		$response = json_decode($response, true);

		if(isset($response['feed']) && isset($response['feed']['entry']) && is_array($response['feed']['entry'])){

			foreach ($response['feed']['entry'] as $entry){
				$author = (isset($entry['author']) && isset($entry['author']['name'])) ? $entry['author']['name']['_value'] : '';
				$snippet = isset($entry['summary']) ? $entry['summary']['_value'] : '';

				if(isset($entry['link']) && is_array($entry['link']) && isset($entry['link'][0])){

					if($entry['link'][0]['title'] == '') continue;
					if($entry['link'][0]['href'] == '') continue;

					if(array_search($entry['link'][0]['href'], $documents_found) !== false) continue;

					$documents_found[] = $entry['link'][0]['href'];

					$documents[] = [
						'title' => $entry['link'][0]['title'],
						'url' => $entry['link'][0]['href'],
						'organization_name' => $author,
						'snippet' => $snippet
					];
				}
			}
		}

		return $documents;
	}


	public function getPatientEducationResources($params) {
		return $this->pe->load($params)->all();
	}

	public function getPatientEducationResource($params) {
		return $this->pe->load($params)->one();
	}

	public function addPatientEducationResource($params) {
		return $this->pe->save($params);
	}

	public function updatePatientEducationResource($params) {
		return $this->pe->save($params);
	}

	public function destroyPatientEducationResource($params) {
		return $this->pe->destroy($params);
	}

	public function getEducationResources($params) {
		return $this->e->load($params)->all();
	}

	public function getEducationResource($params) {
		return $this->e->load($params)->one();
	}

	public function addEducationResource($params) {
		return $this->e->save($params);
	}

	public function updateEducationResource($params) {
		return $this->e->save($params);
	}

	public function destroyEducationResource($params) {
		return $this->e->destroy($params);
	}

}