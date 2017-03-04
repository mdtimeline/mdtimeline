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