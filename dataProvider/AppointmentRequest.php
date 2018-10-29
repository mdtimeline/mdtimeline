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


		$_where = $this->a->whereHandler($params->filter);

		$sql = "SELECT patient_appointment_requests.*,
					   p.pubpid AS pubpid,
					   p.fname AS fname,
					   p.mname AS mname,
					   p.lname AS lname,
					   p.sex AS sex,
					   p.DOB AS DOB,
					   p.phone_home AS phone_home,
					   p.phone_mobile AS phone_mobile,
					   p.phone_publicity AS phone_publicity,
					   
					   u.fname AS provider_fname,
					   u.mname AS provider_mname,
					   u.lname AS provider_lname,
					   u.npi AS provider_npi,
					   
					    GROUP_CONCAT(CONCAT(ic.`name`, ' (', pi.insurance_type ,')' ) ) AS insurance_companies
					   
				  FROM patient_appointment_requests
			 LEFT JOIN patient AS p ON p.pid = patient_appointment_requests.pid
			 LEFT JOIN users AS u ON u.id = patient_appointment_requests.create_uid
			 LEFT JOIN patient_insurances AS pi ON pi.pid = patient_appointment_requests.pid AND pi.insurance_type IN ('A', 'C', 'S')
			 LEFT JOIN insurance_companies AS ic ON ic.id = pi.insurance_id
			 {$_where}
			 GROUP BY patient_appointment_requests.id";

		$results = $this->a->sql($sql)->all();

		return $results;
	}
}