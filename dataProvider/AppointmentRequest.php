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
class AppointmentRequest {

	private $a;

	public function __construct() {
        if($this->a == NULL)
            $this->a = MatchaModel::setSenchaModel('App.model.patient.AppointmentRequest');
	}

	public function getAppointmentRequests($params) {
		return $this->a->load($params)
			->leftJoin(['Term' => 'procedure1'],'sct_descriptions','procedure1_code','ConceptId')
			->leftJoin(['Term' => 'procedure2'],'sct_descriptions','procedure2_code','ConceptId')
			->leftJoin(['Term' => 'procedure3'],'sct_descriptions','procedure3_code','ConceptId')->all();
	}

	public function getAppointmentRequest($params) {
		return $this->a->load($params)
			->leftJoin(['Term' => 'procedure1'],'sct_descriptions','procedure1_code','ConceptId')
			->leftJoin(['Term' => 'procedure2'],'sct_descriptions','procedure2_code','ConceptId')
			->leftJoin(['Term' => 'procedure3'],'sct_descriptions','procedure3_code','ConceptId')->one();
	}

	public function addAppointmentRequest($params) {
		return $this->a->save($params);
	}

	public function updateAppointmentRequest($params) {
		return $this->a->save($params);
	}

	public function deleteAppointmentRequest($params) {
		return $this->a->destroy($params);
	}

	public function getAppointmentRequestReport($params) {
		$results = $this->a->load($params)
			->leftJoin([
				'pubpid' => 'pubpid',
				'fname' => 'fname',
				'mname' => 'mname',
				'lname' => 'lname',
				'sex' => 'sex',
				'language' => 'language',
				'DOB' => 'DOB',
				'phone_home' => 'phone_home',
				'phone_mobile' => 'phone_mobile',
				'phone_publicity' => 'phone_publicity'
			], 'patient', 'pid', 'pid')
			->leftJoin([
				'fname' => 'provider_fname',
				'mname' => 'provider_mname',
				'lname' => 'provider_lname',
				'npi' => 'provider_npi'
			],'users','create_uid','id')->all();

		return $results;
	}
}