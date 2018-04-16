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
include_once(ROOT .'/dataProvider/User.php');
class Insurance {

	/**
	 * @var MatchaCUP
	 */
	private $ic;
	/**
	 * @var MatchaCUP
	 */
	private $pi;

	function __construct(){
        $this->ic = MatchaModel::setSenchaModel('App.model.administration.InsuranceCompany');
        $this->pi = MatchaModel::setSenchaModel('App.model.patient.Insurance');
        $this->pic = MatchaModel::setSenchaModel('App.model.patient.InsuranceCover');


        \Matcha::setAppDir(ROOT.'/modules');

        if(!isset($this->insurancesdata))
            $this->insurancesdata = \MatchaModel::setSenchaModel('Modules.billing.model.BillingInsurancesData');

        \Matcha::setAppDir(ROOT.'/app');
	}

	/** Companies */
	public function getInsuranceCompanies($params) {
		return $this->ic->load($params)->all();
	}

	public function getInsuranceCompany($params) {
		return $this->ic->load($params)->one();
	}

	public function addInsuranceCompany($params) {
		return $this->ic->save($params);
	}

	public function updateInsuranceCompany($params) {
		return $this->ic->save($params);
	}

	public function destroyInsuranceCompany($params) {
		return $this->ic->destroy($params);
	}

	public function getInsuranceCovers($params) {
        return $this->pic->load($params)->RightJoin(
		    [
		        'title' => 'departments_title',
                'code'  => 'departments_code'
            ], 'departments', 'department_code', 'code'
        )->all();
	}

	public function getInsuranceCover($params) {
		return $this->pic->load($params)->RightJoin(
            [
                'title' => 'departments_title',
                'code'  => 'departments_code'
            ], 'departments', 'department_code', 'code'
        )->one();
	}

	public function addInsuranceCover($params) {
		return $this->pic->save($params);
	}

	public function updateInsuranceCover($params) {
		return $this->pic->save($params);
	}

	public function destroyInsuranceCover($params) {
		return $this->pic->destroy($params);
	}


	/** Patient */

	/***
	 * @param $params
	 * @return mixed
	 */
	public function getInsurances($params) {

        if (isset($params->filter[0]->value))
        {
            $patid = $params->filter[0]->value;
        }
        else
            {
                $patid = false ;
            }

        $exeValues = [];

        if ($patid != false )
        {
            $exeValues['patid'] = $patid;
        }

        #region Sql Load Patient Insurances Data

        $sqlLoad = "Select * from patient_insurances 
                    Left Join insurance_companies on patient_insurances.insurance_id = insurance_companies.id 
                    Left Join acc_billing_insurance_data on insurance_companies.code = acc_billing_insurance_data.ins_code 
                    where patient_insurances.pid = :patid ";

        #endregion

        $getRecords = $this->pi->sql($sqlLoad)->all($exeValues);

        return $getRecords;

	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function getInsurance($params) {
        return $this->pi->load($params)->LeftJoin(
            [
                'ins_synonym' => 'ins_synonym'
            ], 'acc_billing_insurance_data', 'acc_billing_insurance_data.ins_code', 'insurance_companies.code'
        )->one();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function addInsurance($params) {
		return $this->pi->save($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function updateInsurance($params) {
		return $this->pi->save($params);
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public function destroyInsurance($params) {
		return $this->pi->destroy($params);
	}


	public function getInsuranceCompanyById($id){
		$this->ic->addFilter('id', $id);
		return $this->ic->load()->one();
	}

	public function getPatientPrimaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'p');
		return $this->pi->load()->one();
	}

	public function getPatientSecondaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'S');
		return $this->pi->load()->one();
	}

	public function getPatientComplementaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'C');
		return $this->pi->load()->one();
	}
} 