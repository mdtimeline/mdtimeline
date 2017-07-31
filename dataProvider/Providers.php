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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Providers {
	/**
	 * @var MatchaCUP
	 */
	private $pc;

	function getProviderCredentializationModel(){
        if(!isset($this->pc))
            $this->pc = MatchaModel::setSenchaModel('App.model.administration.ProviderCredentialization');
		return $this->pc;
	}

	public function getProviderCredentializations($params){
		$this->getProviderCredentializationModel();
		$this->pc->setOrFilterProperties(array('provider_id'));
		if(isset($params->fullList)){

			$sql = "SELECT `ic`.`id` as insurance_company_id, `ic`.`name` as insurance_company_name,
					(SELECT id FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider1) as id,
					:provider2 as 'provider_id',
					(SELECT start_date FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider3) as start_date,
					(SELECT end_date FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider4) as end_date,
					(SELECT credentialization_notes FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider5) as credentialization_notes,
					(SELECT create_uid FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider6) as create_uid,
					(SELECT create_date FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider7) as create_date,
					(SELECT update_uid FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider8) as update_uid,
					(SELECT update_date FROM provider_credentializations as pc  WHERE pc.insurance_company_id = ic.id and pc.provider_id = :provider9) as update_date
				 FROM `insurance_companies` as ic";

			$params = array(
				':provider1' => $params->providerId,
				':provider2' => $params->providerId,
				':provider3' => $params->providerId,
				':provider4' => $params->providerId,
				':provider5' => $params->providerId,
				':provider6' => $params->providerId,
				':provider7' => $params->providerId,
				':provider8' => $params->providerId,
				':provider9' => $params->providerId
			);

			return $this->pc->sql($sql)->all($params);
		}

		return $this->pc->load($params)->all();
	}

	public function getProviderCredentialization($params){
		$this->getProviderCredentializationModel();
		return $this->pc->load($params)->one();
	}

	public function addProviderCredentialization($params){
		$this->getProviderCredentializationModel();
		return $this->pc->save($params);
	}

	public function updateProviderCredentialization($params){
		$this->getProviderCredentializationModel();
		return $this->pc->save($params);
	}

	public function deleteProviderCredentialization($params){
		$this->getProviderCredentializationModel();
		return $this->pc->destroy($params);
	}

	public function getProviderCredentializationForDate($provider_id, $insurance_id, $date = null){
		$this->getProviderCredentializationModel();
		$this->pc->addFilter('provider_id', $provider_id);
		$this->pc->addFilter('insurance_company_id', $insurance_id);
		if(isset($date)){
			$this->pc->addFilter('start_date', $date, '<=');
			$this->pc->addFilter('end_date', $date, '>=');
		}
		return $this->pc->load()->one();
	}

	public function npiRegistrySearch($params){
		return $this->npiRegistrySearchByNpi($params->query);
	}

	public function npiRegistrySearchByNpi($nip){

		if(strlen($nip) !== 10 || !$this->isNpiValid($nip)){
			return [
				'success' => false,
				'error' => 'NPI not valid'
			];
		}

		$url = 'https://npiregistry.cms.hhs.gov/api/?number=' . $nip;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$return = json_decode($output, true);

		if($return['result_count'] == 0){
			return [
				'success' => true,
				'data' => false
			];
		}

		return [
			'success' => true,
			'data' => $return['results'][0]
		];
	}

	/**
	 * @param $npi
	 * @return bool
	 */
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
			if(is_nan($npi{$i})){
				return false;
			}
			$tmp = $npi{$i} - '0';
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