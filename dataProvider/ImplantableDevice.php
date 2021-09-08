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
class ImplantableDevice {

	/**
	 * @var bool|MatchaCUP
	 */
	private $i;

	function __construct(){
		$this->i = MatchaModel::setSenchaModel('App.model.patient.ImplantableDevice');
	}

	public function getPatientImplantableDevices($params){
		return $this->i->load($params)->all();
	}

	public function getPatientImplantableDevice($params){
		return $this->i->load($params)->one();
	}

	public function addPatientImplantableDevice($params){
		return $this->i->save($params);
	}

	public function updatePatientImplantableDevice($params){
		return $this->i->save($params);
	}

	public function getPatientImplantableDeviceByPidAndDates($pid, $start = null, $end = null){

		$this->i->addFilter('pid', $pid);

		if(isset($start)){
			$this->i->addFilter('implanted_date', $start, '>=');
		}
		if(isset($end)) {
			$this->i->addFilter('implanted_date', $end, '<=');
		}

		return $this->i->load()->all();
	}

    public function getPatientImplantableDeviceByEid($eid)
    {
        $params = new stdClass();
        $params->filter[0] = new stdClass();
        $params->filter[0]->property = 'eid';
        $params->filter[0]->value = $eid;
        return $this->getPatientImplantableDevices($params);
    }

	public function getUidData($params){
		if(!isset($params->udi)){
			return [
				'success' => false,
				'error' => 'UDI missing'
			];
		}
		$response = [];
		$response['parse'] = $this->parseUid($params);
		$response['lookup'] = $this->lookup($params);
		$response['snomed'] = $this->devicesSnomed($params);

		return $response;
	}

	public function parseUid($params){

		if(!isset($params->udi)){
			return [
				'success' => false,
				'error' => 'UDI missing'
			];
		}
		return $this->get('https://accessgudid.nlm.nih.gov/api/v2/parse_udi.json', (array) $params);
	}

	public function lookup($params){
		if(!isset($params->udi) && !isset($params->di)){
			return [
				'success' => false,
				'error' => 'DI OR UDI missing'
			];
		}

		return $this->get('https://accessgudid.nlm.nih.gov/api/v2/devices/lookup.json', (array) $params);
	}

	public function devicesImplantableList($params){
		$params = (array) $params;
		return $this->get('https://accessgudid.nlm.nih.gov/api/v2/devices/implantable/list.json', $params);
	}

	public function devicesSnomed($params){
		$params = (array) $params;

		$ticket = $this->getTicket('vela1606', 'S3cur3121201!');

		return $this->get('https://accessgudid.nlm.nih.gov/api/v2/devices/snomed.json', $params, $ticket);
	}

	private function get($url, $params, $ticket = null){

		if(isset($ticket)){
			$params['ticket'] = $ticket;
		}

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

	private function getTicket($username, $password){

		$ticket = false;

		$params = [
			'username' => $username,
			'password' => $password
		];

		$url = 'https://utslogin.nlm.nih.gov/cas/v1/tickets';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($params));
		$response = curl_exec($curl);
		curl_close($curl);

		preg_match('/action="(.*?)"/', $response, $matches);

		if(isset($matches[1])){
			$action = $matches[1];
		}else{
			return $ticket;
		}
		$params = [
			'service' => 'http://umlsks.nlm.nih.gov'
		];
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $action);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($params));
		$ticket = curl_exec($curl);
		curl_close($curl);

		return $ticket;
	}
}
