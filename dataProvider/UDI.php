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
class UDI {

	public function parseUid($params){
		if(!isset($params->uid)){
			return [
				'success' => false,
				'error' => 'UDI missing'
			];
		}
		return $this->get('https://accessgudid.nlm.nih.gov/api/v1/parse_udi.json', (array) $params);
	}

	public function lookup($params){
		if(!isset($params->uid) && !isset($params->di)){
			return [
				'success' => false,
				'error' => 'DI OR UDI missing'
			];
		}

		return $this->get('https://accessgudid.nlm.nih.gov/api/v1/lookup.json', (array) $params);
	}

	public function devicesImplantableList($params){
		$params = (array) $params;
		return $this->get('https://accessgudid.nlm.nih.gov/api/v1/devices/implantable/list.json', $params);
	}

	public function devicesSnomed($params){
		$params = (array) $params;
		return $this->get('https://accessgudid.nlm.nih.gov/api/v1/devices/snomed.json', $params);
	}

	private function get($url, $params){
		$url .= '?' . http_build_query($params);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);

		if(!$response){
			return [
				'success' =>false,
				'error' => 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl)
			];
		}

		return [
			'success' => true,
			'data' => json_decode($response, true)
		];
	}
}
